<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * MediaGroup
 *
 * @ORM\Entity(repositoryClass="Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MediaGroup
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @var Type
     *
     * @ORM\Column(type="media_group_type")
     */
    protected $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $editedOn;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem",
     *     mappedBy="group",
     *     cascade={"persist", "merge", "remove", "detach"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"sequence": "ASC"})
     */
    protected $connectedItems;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $numberOfConnectedItems;

    /**
     * MediaGroup constructor.
     *
     * @param UuidInterface $id
     * @param Type $type
     */
    private function __construct(
        UuidInterface $id,
        Type $type
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->connectedItems = new ArrayCollection();
    }

    /**
     * Create
     *
     * @param Type $type
     * @return MediaGroup
     */
    public static function create(
        Type $type
    ) {
        return new self(
            Uuid::uuid4(),
            $type
        );
    }

    /**
     * @param UuidInterface $id
     * @param Type $type
     * @return MediaGroup
     */
    public static function createForId(
        UuidInterface $id,
        Type $type
    ) {
        return new self(
            $id,
            $type
        );
    }

    /**
     * To array
     *
     * @return array
     */
    public function __toArray()
    {
        // Init $connectedItemsArray
        $connectedItemsArray = array();

        // Loop connected items
        foreach ($this->connectedItems as $connectedItem) {
            // Add connectedItem as an array
            $connectedItemsArray[] = $connectedItem->__toArray();
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'editedOn' => ($this->editedOn) ? $this->editedOn->getTimestamp() : null,
            'connectedItems' => $connectedItemsArray,
        ];
    }

    /**
     * Gets the value of id.
     *
     * @return UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of type.
     *
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets the value of editedOn.
     *
     * @return \DateTime
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }

    /**
     * Gets the value of connectedItems.
     *
     * @return ArrayCollection
     */
    public function getConnectedItems()
    {
        return $this->connectedItems;
    }

    /**
     * @return boolean
     */
    public function hasConnectedItems()
    {
        return ($this->numberOfConnectedItems > 0);
    }

    /**
     * @return int
     */
    public function getNumberOfConnectedItems()
    {
        return $this->numberOfConnectedItems;
    }

    /**
     * @param $mediaItemId
     * @return mixed
     */
    public function getConnectedItemByMediaItemId(
        $mediaItemId
    ) {
        /** @var MediaGroupMediaItem $mediaGroupMediaItem */
        foreach ($this->connectedItems->toArray() as $mediaGroupMediaItem) {
            if ($mediaGroupMediaItem->getItem()->getId() == (int) $mediaItemId) {
                return $mediaGroupMediaItem;
            }
        }
    }

    /**
     * Add connected item
     *
     * @param MediaGroupMediaItem $connectedItem
     * @return MediaGroup
     */
    public function addConnectedItem(
        MediaGroupMediaItem $connectedItem
    ) {
        $this->connectedItems->add($connectedItem);

        // This is required, otherwise, doctrine thinks the entity hasn't been changed
        $this->setNumberOfConnectedItems();

        return $this;
    }

    /**
     * Remove connected item
     *
     * @param MediaGroupMediaItem $connectedItem
     * @return MediaGroup
     */
    public function removeConnectedItem(
        MediaGroupMediaItem $connectedItem
    ) {
        $this->connectedItems->removeElement($connectedItem);

        // This is required, otherwise, doctrine thinks the entity hasn't been changed
        $this->setNumberOfConnectedItems();

        return $this;
    }

    private function setNumberOfConnectedItems()
    {
        $this->numberOfConnectedItems = $this->connectedItems->count();
    }

    /**
     * Gets the value of connectedItems.
     *
     * @return ArrayCollection
     */
    public function getIdsForConnectedItems()
    {
        $ids = array();

        foreach ($this->connectedItems as $connectedItem) {
            $ids[] = $connectedItem->getItem()->getId();
        }

        return $ids;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->editedOn = new \Datetime();
        $this->setNumberOfConnectedItems();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->editedOn = new \Datetime();
        $this->setNumberOfConnectedItems();
    }
}
