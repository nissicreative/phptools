<?php
namespace Nissi\Ecommerce;

use Iterator;
use Countable;
use Nissi\Contracts\Orderable;

abstract class AbstractShoppingCart implements Iterator, Countable
{
    // Array stores the list of items in the cart:
    protected $items = [];

    // For tracking iterations:
    protected $position = 0;

    // For storing the IDs, as a convenience:
    protected $ids = [];

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
     */
    public function __construct()
    {
        $this->items = [];
        $this->ids   = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Items
    |--------------------------------------------------------------------------
     */

    /*
     * Returns a Boolean indicating if the cart is empty.
     */
    public function isEmpty()
    {
        return (empty($this->items));
    }

    /*
     * Adds a new item to the cart.
     */
    public function addItem(Orderable $item, $qty = 1)
    {
        // Need the item id:
        $id = $item->getId();

        // Add or update:
        if (isset($this->items[$id])) {
            $this->updateItem($item, $this->items[$id]['qty'] + $qty);
        } else {
            $this->items[$id] = ['item' => $item, 'qty' => $qty];
            $this->ids[]      = $id; // Store the id, too!
        }
    }

    /*
     * Updates an item's existence/quantity in the cart.
     */
    public function updateItem(Orderable $item, $qty = null)
    {
        // Need the unique item id:
        $id = $item->getId();

        // Delete, add, or update accordingly:
        if ($qty === 0) {
            $this->deleteItem($item);
        } elseif ( ! isset($this->items[$id])) {
            $this->addItem($item, $qty);
        } elseif (($qty > 0)) {
            $this->items[$id]['qty'] = $qty;
        }
    }

    /*
     * Removes an item from the cart.
     */
    public function deleteItem(Orderable $item)
    {
        // Need the unique item id:
        $id = $item->getId();

        // Remove it:
        if (isset($this->items[$id])) {
            unset($this->items[$id]);

            // Remove the stored id, too:
            $index = array_search($id, $this->ids);
            unset($this->ids[$index]);

            // Recreate that array to prevent holes:
            $this->ids = array_values($this->ids);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Aggregate Info
    |--------------------------------------------------------------------------
     */

    public function getSubtotal()
    {
        $subtotal = 0;

        foreach ($this->items as $id => $data) {
            $item = $data['item'];
            $qty  = $data['qty'];

            $subtotal += $item->getPrice() * $qty;
        }

        return $subtotal;
    }

    public function getTax($location = null)
    {
        return 0;
    }

    public function getShipping($location = null)
    {
        return 0;
    }

    public function getTotal()
    {
        return $this->getSubtotal() + $this->getTax() + $this->getShipping();
    }

    public function getItems()
    {
        return $this->items;
    }

    /*
    |--------------------------------------------------------------------------
    | Required by Interfaces
    |--------------------------------------------------------------------------
     */

    /*
     *  Required by Iterator; returns the current value.
     */
    public function current()
    {
        // Get the index for the current position:
        $index = $this->ids[$this->position];

        // Return the item:
        return $this->items[$index];
    }

    /*
     * Required by Iterator; returns the current key.
     */
    public function key()
    {
        return $this->position;
    }

    /*
     * Required by Iterator; increments the position.
     */
    public function next()
    {
        $this->position++;
    }

    /*
     * Required by Iterator; returns the position to the first spot.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /*
     * Required by Iterator; returns a Boolean indiating if a value is indexed at this position.
     */
    public function valid()
    {
        return (isset($this->ids[$this->position]));
    }

    /*
     * Required by Countable.
     */
    public function count()
    {
        return count($this->items);
    }

}
