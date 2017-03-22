<?php

namespace Perfumer\Component\Contracts\Example\Contract\Controller;

use Perfumer\Component\Contracts\Annotations\Call;
use Perfumer\Component\Contracts\Annotations\Collection;
use Perfumer\Component\Contracts\Annotations\Context;
use Perfumer\Component\Contracts\Annotations\Custom;
use Perfumer\Component\Contracts\Annotations\Error;
use Perfumer\Component\Contracts\Annotations\Extend;
use Perfumer\Component\Contracts\Annotations\Output;
use Perfumer\Component\Contracts\Annotations\Property;
use Perfumer\Component\Contracts\Annotations\ServiceParent;
use Perfumer\Component\Contracts\Annotations\ServiceProperty;
use Perfumer\Component\Contracts\Annotations\Template;

/**
 * @Extend(class="\Perfumer\Component\Contracts\Example\ParentController")
 * @Context(name="validators", class="\Perfumer\Component\Contracts\Example\Context\FooContext")
 */
interface FooController
{
    /**
     * @Call (na="validators", me="intType", ar={"param1"}, re="param1_valid",                    va=true)
     * @Call (na="validators", me="intType", ar={"param2"}, re="param2_valid", if="param1_valid", va=true)
     * @Collection(steps={
     *   @Call           (na="validators", me="sum",              ar={"param1", "param2"},                   re=@Property("sum")),
     *   @Custom         (                 me="sumDoubled",       ar={@Property("sum")},                     re="double_sum"),
     *   @ServiceParent  (                 me="sandboxActionTwo", ar={@Property("sum"), @Property("staff")}, re={"sand", "box"}),
     *   @ServiceProperty(na="foobar",     me="baz",              ar={"sand", "box"},                        re=@Output)
     * })
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function bar(int $param1, int $param2);

    /**
     * @Call           (na="validators", me="intType",          ar={"param1"},                  re="param1_valid", va=true)
     * @Call           (na="validators", me="intType",          ar={"param2"},                  re="param2_valid", va=true)
     * @Call           (na="validators", me="sum",              ar={"param1", "param2"},        re="sum")
     * @ServiceParent  (                 me="sandboxActionTwo", ar={"sum", @Property("staff")}, re="sandbox")
     * @ServiceProperty(na="foobar",     me="baz",              ar={@Context("validators")},    re=@Output)
     * @Error          (na="validators", me="fooErrors",        ar={"param1_valid", "param2_valid"})
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function baz(int $param1, int $param2);
}
