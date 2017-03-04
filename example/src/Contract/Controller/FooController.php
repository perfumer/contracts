<?php

namespace Perfumer\Component\Bdd\Example\Contract\Controller;

use Perfumer\Component\Bdd\Annotations\Call;
use Perfumer\Component\Bdd\Annotations\Collection;
use Perfumer\Component\Bdd\Annotations\Context;
use Perfumer\Component\Bdd\Annotations\Errors;
use Perfumer\Component\Bdd\Annotations\Extend;
use Perfumer\Component\Bdd\Annotations\Service;
use Perfumer\Component\Bdd\Annotations\Template;
use Perfumer\Component\Bdd\Annotations\Validate;

/**
 * @Extend(class = "\Perfumer\Component\Bdd\Example\ParentController")
 * @Context(name = "validators", class = "\Perfumer\Component\Bdd\Example\Context\FooContext")
 */
interface FooController
{
    /**
     * @Validate (name = "validators", method = "intType",          arguments = {"param1"},            return = "param1_valid")
     * @Validate (name = "validators", method = "intType",          arguments = {"param2"},            return = "param2_valid")
     * @Collection(steps = {
     *   @Call   (name = "validators", method = "sum",              arguments = {"param1", "param2"},  return = "sum"),
     *   @Service(name = "_parent",    method = "sandboxActionTwo", arguments = {"sum", "this.staff"}, return = "sandbox"),
     *   @Service(name = "foobar",     method = "baz",              arguments = {"sandbox"},           return = "_return")
     * })
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function bar(int $param1, int $param2);

    /**
     * @Validate(name = "validators", method = "intType",          arguments = {"param1"},            return="param1_valid")
     * @Validate(name = "validators", method = "intType",          arguments = {"param2"},            return="param2_valid")
     * @Call    (name = "validators", method = "sum",              arguments = {"param1", "param2"},  return = "sum")
     * @Service (name = "_parent",    method = "sandboxActionTwo", arguments = {"sum", "this.staff"}, return = "sandbox")
     * @Service (name = "foobar",     method = "baz",              arguments = {"sandbox"},           return = "_return")
     * @Errors  (name = "validators", method = "fooErrors",        arguments = {"param1_valid", "param2_valid"})
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function baz(int $param1, int $param2);
}
