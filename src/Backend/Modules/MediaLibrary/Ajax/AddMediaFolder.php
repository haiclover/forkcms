<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\CreateMediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Event\MediaFolderCreated;
use Common\Uri;

/**
 * This AJAX-action will add a new MediaFolder.
 */
class AddMediaFolder extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        /** @var MediaFolder|null $parent */
        $parent = $this->getParent();

        /** @var string $name */
        $name = $this->getFolderName($parent);

        /** @var CreateMediaFolder $createMediaFolder */
        $createMediaFolder = new CreateMediaFolder(
            $name,
            $parent,
            BackendAuthentication::getUser()->getUserId()
        );

        // Handle the MediaFolder create
        $this->get('command_bus')->handle($createMediaFolder);
        $this->get('event_dispatcher')->dispatch(
            MediaFolderCreated::EVENT_NAME,
            new MediaFolderCreated($createMediaFolder->mediaFolder)
        );

        // Success message
        $this->output(
            self::OK,
            $createMediaFolder->mediaFolder->__toArray(),
            vsprintf(
                Language::msg('AddedFolder'),
                array(
                    $createMediaFolder->mediaFolder->getId()
                )
            )
        );
    }

    /**
     * Get name
     *
     * @param MediaFolder|null $parent
     * @return string
     */
    protected function getFolderName($parent = null)
    {
        // Define name
        $name = trim(\SpoonFilter::getPostValue('name', null, '', 'string'));

        // Urlise name
        $name = Uri::getUrl($name);

        // We don't have a name
        if ($name === '') {
            // Throw output error
            $this->output(
                self::BAD_REQUEST,
                null,
                Language::err('NameIsRequired')
            );
        }

        // Folder name already exists
        if ($this->get('media_library.repository.folder')->existsByName($name, $parent)) {
            // Throw output error
            $this->output(
                self::BAD_REQUEST,
                null,
                Language::err('MediaFolderExists')
            );
        }

        return $name;
    }

    /**
     * Get parent
     *
     * @return MediaFolder|null
     */
    protected function getParent()
    {
        // Get parameters
        $parentId = (int) trim(\SpoonFilter::getPostValue(
            'parent_id',
            null,
            null,
            'int'
        ));

        // We have a parent
        if ($parentId !== null) {
            try {
                /** @var MediaFolder */
                return $this->get('media_library.repository.folder')->getOneById($parentId);
            } catch (\Exception $e) {
                // Throw output error
                $this->output(
                    self::BAD_REQUEST,
                    null,
                    Language::err('ParentNotExists')
                );
            }
        }

        return null;
    }
}
