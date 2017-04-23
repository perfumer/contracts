<?php

namespace Perfumer\Component\Contracts\Example\Contract\Controller;

use Perfumer\Component\Contracts\Annotations\Call;
use Perfumer\Component\Contracts\Annotations\Collection;
use Perfumer\Component\Contracts\Annotations\Context;
use Perfumer\Component\Contracts\Annotations\Custom;
use Perfumer\Component\Contracts\Annotations\Error;
use Perfumer\Component\Contracts\Annotations\Extend;
use Perfumer\Component\Contracts\Annotations\Inject;
use Perfumer\Component\Contracts\Annotations\Output;
use Perfumer\Component\Contracts\Annotations\Property;
use Perfumer\Component\Contracts\Annotations\ServiceObject;
use Perfumer\Component\Contracts\Annotations\ServiceParent;
use Perfumer\Component\Contracts\Annotations\ServiceProperty;
use Perfumer\Component\Contracts\Annotations\Template;

/**
 * @Extend(class="\Perfumer\Component\Contracts\Example\ParentController")
 * @Context(name="validators", class="\Perfumer\Component\Contracts\Example\Context\FooContext")
 * @Inject(name="iterator", type="\Iterator")
 * @Inject(name="date", type="\DateTime")
 * @Inject(name="some_string", type="string")
 */
interface FooController
{
    /**
     * @Call (name="date", method="format", arguments={@Property("sum")})
     * @Call (name="validators", method="intType", arguments={"param1"}, return="param1_valid")
     * @Call (name="validators", method="intType", arguments={"param2"}, return="param2_valid", if="param1_valid")
     * @Collection(steps={
     *   @Call           (name="validators", method="sum",              arguments={"param1"},                             return=@Property("sum")),
     *   @Custom         (                   method="sumDoubled",       arguments={@Property("sum")},                     return="double_sum"),
     *   @ServiceParent  (                   method="sandboxActionTwo", arguments={@Property("sum"), @Property("staff")}, return={"sand", @Property("box")}),
     *   @ServiceProperty(name="foobar",     method="baz",              arguments={@Inject("date"), @Property("box")},             return=@Output)
     * })
     * @Error (name="validators", method="fooErrors", unless="param1_valid")
     * @Error (name="validators", method="fooErrors", unless="param2_valid")
     *
     * @param int $param1
     * @param Output $param2
     * @return string
     */
    public function barAction(int $param1, Output $param2): string;

    /**
     * @Call           (name="validators", method="intType",          arguments={"param1"},                  return="param1_valid")
     * @Call           (name="validators", method="intType",          arguments={"param2"},                  return="param2_valid")
     * @Call           (name="validators", method="sum",              arguments={"param1"},                  return="sum")
     * @ServiceParent  (                   method="sandboxActionTwo", arguments={"sum", @Property("staff")}, return="sandbox")
     * @ServiceProperty(name="foobar",     method="baz",              arguments={@Context("validators")},    return=@Output)
     * @ServiceObject  (name="sandbox",    method="execute")
     * @Error          (name="validators", method="fooErrors", unless="param1_valid")
     * @Error          (name="validators", method="fooErrors", unless="param2_valid")
     *
     * @param int $param1
     * @param int $param2
     * @return \DateTime
     */
    public function bazAction(int $param1, int $param2): \DateTime;
}
