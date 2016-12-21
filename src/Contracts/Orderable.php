<?php
namespace Nissi\Contracts;

interface Orderable
{
    public function getId();
    public function getPrice();
    public function getShortDescription();
}
