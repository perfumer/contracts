<?php

namespace Barman\Example\Contract\Example1Context;

class Example1Context_Product
{

    public function onCreate() : void
    {
        $this->class = 'Barman\Example\Contract/Example1Context';
                $this->method = 'product';
                $this->arguments = [$this->a, $this->b];
                $this->return = $this->out;

                parent::onCreate();
    }


}
