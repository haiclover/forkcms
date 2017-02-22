<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Core\Engine\DataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Engine\Model as BackendMediaLibraryModel;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class MediaItemDataGrid extends DataGridDB
{
    /**
     * MediaItemDataGrid constructor.
     *
     * @param string $type
     * @param integer|null $folderId
     */
    public function __construct($type, $folderId = null)
    {
        $andWhere = '';
        $parameters = [(string) $type];

        if ($folderId !== null) {
            $andWhere .= ' AND i.mediaFolderId = ?';
            $parameters[] = (int) $folderId;
        }

        parent::__construct(
            'SELECT i.id, i.type, i.url, i.title, i.shardingFolderName, COUNT(gi.mediaItemId) as num_connected, i.mime, UNIX_TIMESTAMP(i.createdOn) AS createdOn
             FROM MediaItem AS i
             LEFT OUTER JOIN MediaGroupMediaItem as gi ON gi.mediaItemId = i.id
             WHERE i.type = ?'
                . $andWhere
                . ' GROUP BY i.id',
            $parameters
        );

        // filter on folder?
        if ($folderId !== null) {
            // set the URL
            $this->setURL('&folder=' . $folderId, true);
        }

        // define editActionUrl
        $editActionUrl = Model::createURLForAction('EditMediaItem');

        // set headers
        $this->setHeaderLabels(
            array(
                'type' => '',
                'url' => ucfirst(Language::lbl('Image')),
            )
        );

        // active tab
        $this->setActiveTab('tab' . ucfirst($type));

        // hide column
        // If we have an image, show the image
        if ($type == Type::IMAGE) {
            $this->setColumnsHidden(array('shardingFolderName', 'type', 'mime'));
        } else {
            $this->setColumnsHidden(array('shardingFolderName', 'type', 'mime', 'url'));
        }

        // sorting columns
        $this->setSortingColumns(
            array(
                'createdOn',
                'url',
                'title',
                'num_connected',
                'mime'
            ),
            'title'
        );
        $this->setSortParameter('asc');

        // set column URLs
        $this->setColumnURL(
            'title',
            $editActionUrl
            . '&id=[id]'
            . (($folderId) ? '&folder=' . $folderId : '')
        );

        $this->setColumnURL(
            'num_connected',
            $editActionUrl
            . '&id=[id]'
            . (($folderId) ? '&folder=' . $folderId : '')
        );

        // If we have an image, show the image
        if ($type === Type::IMAGE) {
            // Add image url
            $this->setColumnFunction(
                array(new BackendDataGridFunctions(), 'showImage'),
                array(
                    MediaItem::getUploadRootDir('backend') . '/[shardingFolderName]',
                    '[url]',
                    '[url]',
                    Model::createURLForAction('EditMediaItem')
                    . '&id=[id]'
                    . '&folder=' . $folderId,
                    BackendMediaLibraryModel::BACKEND_THUMBNAIL_WIDTH,
                    BackendMediaLibraryModel::BACKEND_THUMBNAIL_HEIGHT
                ),
                'url',
                true
            );
        }

        // set column functions
        $this->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getLongDate'),
            array('[createdOn]'),
            'createdOn',
            true
        );

        // add edit column
        $this->addColumn(
            'edit',
            null,
            Language::lbl('Edit'),
            $editActionUrl
            . '&id=[id]'
            . '&folder=' . $folderId,
            Language::lbl('Edit')
        );

        // our JS needs to know an id, so we can highlight it
        $this->setRowAttributes(array('id' => 'row-[id]'));

        // add checkboxes
        $this->setMassActionCheckboxes('check', '[id]');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown(
            'action',
            array(
                'move' => Language::lbl('MoveMedia'),
                'delete' => Language::lbl('DeleteMediaItems')
            ),
            'move',
            false,
            'form-control',
            'form-control danger'
        );
        $ddmMassAction->setAttribute(
            'id',
            'mass-action-' . $type
        );
        $ddmMassAction->setOptionAttributes(
            'move',
            array(
                'data-target' => '#confirmMoveMediaItems',
            )
        );
        $ddmMassAction->setOptionAttributes(
            'delete',
            array(
                'data-target' => '#confirmDeleteMediaItems',
            )
        );
        $this->setMassAction($ddmMassAction);
    }

    /**
     * @param $type
     * @param null $folderId
     * @return DataGridDB
     */
    public static function getDataGrid($type, $folderId = null)
    {
        $dataGrid = new self($type, $folderId);

        return $dataGrid;
    }

    /**
     * @param $type
     * @param null $folderId
     * @return string
     */
    public static function getHtml($type, $folderId = null)
    {
        $dataGrid = new self($type, $folderId);

        return (string) $dataGrid->getContent();
    }
}
