<?php

namespace App\Cart;

class CartManager
{   
    //id => {product, quantity}
    private $items = [];
    public function addItem($product, $quantity = 1){
        if (array_key_exists($product->getId(), $this->items)){
            $this->items[$product->getId()]->quantity += $quantity;
        }else{
            $this->items[$product->getId()] = new CartItem($product, $quantity);
        }
    }
    public function updateItem($product, $quantity = 1){
        if (array_key_exists($product->getId(), $this->items)){
            $this->items[$product->getId()]->quantity = $quantity;
        }
    }
    public function getTotalItems()
    {
        $totalQuantity = 0;
        foreach ($this->items as $item) {
            $totalQuantity += $item->getQuantity();
        }
        return $totalQuantity;
    }
    public function removeItem($product){
        if (array_key_exists($product->getId(), $this->items)){
            unset($this->items[$product->getId()]);
        }
    }
    public function getAmount(){
        $total = 0;
        foreach($this->items as $key => $item){
            $total += $item->getAmount();
        }
        return $total;
    }
    public function getItems(){
        return $this->items;
    }
}
