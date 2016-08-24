<?php

namespace Greg\Orm\Table;

class Rows extends RowAbstract
{
    public function delete()
    {
        foreach($this->items() as $row) {
            $row->delete();
        }

        return $this;
    }

    public function items()
    {
        return $this->toArray(false);
    }

    public function toArray($recursive = true)
    {
        $items = $this->getStorage();

        if ($recursive) {
            foreach($items as $key => $item) {
                if ($item instanceof Row) {
                    $items[$key] = $item->toArray();
                }
            }
            unset($item);
        }

        return $items;
    }

    public function toArrayObject($recursive = true)
    {
        $items = parent::toArrayObject();

        if ($recursive) {
            foreach($items as &$item) {
                if ($item instanceof Row) {
                    $item = $item->toArrayObject();
                }
            }
            unset($item);
        }

        return $items;
    }
}