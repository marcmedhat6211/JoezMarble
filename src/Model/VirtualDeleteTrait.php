<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

trait VirtualDeleteTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deleted = null;

    /**
     * @ORM\Column(name="deleted_by", type="string", length=30, nullable=true)
     */
    protected $deletedBy = NULL;

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    public function getDeletedBy()
    {
        return $this->deletedBy;
    }
}