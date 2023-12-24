<?php

namespace App\Models;

use App\Http\Resources\ProductResource;

class CartItem
{
    public $uuid;
    public $product;
    public $options;
    public $quantity;


    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function  getSubtotal()
    {
        $options = $this->options;
        $product = $this->product;

        $value = $product->price;

        if ($product->discountPrice > 0) {
            $value = $product->discountPrice;
        }

        foreach ($options as $option) {
            $value += $option->price;
        }

        return $value;
    }

    public function  getTotal()
    {
        return $this->getSubtotal() * $this->quantity;
    }

    public function  getJson()
    {
        return [
            'uuid' => $this->uuid,
            'product' => ProductResource::make($this->product),
            'options' => $this->options,
            'quantity' => $this->quantity,
            'subtotal' => $this->getSubtotal(),
            'total' => $this->getTotal(),
        ];
    }
}
