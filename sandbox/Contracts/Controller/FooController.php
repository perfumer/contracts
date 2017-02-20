<?php

namespace Perfumer\Component\Bdd\Sandbox\Contracts\Controller;

use Perfumer\Component\Bdd\Annotations\Call;
use Perfumer\Component\Bdd\Annotations\Context;
use Perfumer\Component\Bdd\Annotations\Custom;
use Perfumer\Component\Bdd\Annotations\Extend;
use Perfumer\Component\Bdd\Annotations\Service;
use Perfumer\Component\Bdd\Annotations\Validate;

/**
 * @Extend(class = "\Perfumer\Component\Bdd\Sandbox\ParentController")
 * @Context(name = "validators", class = "\Perfumer\Component\Bdd\Sandbox\Contexts\FooContext")
 */
interface FooController
{
    /**
     * @Validate(name = "validators", method = "intType", arguments = {"param1"})
     * @Validate(name = "validators", method = "intType", arguments = {"param2"})
     * @Call(name = "validators", method = "sum", arguments = {"param1", "param2"}, return = "sum")
     * @Custom(name = "getSomeStaff", arguments = {"this.param3"}, return = "this.staff")
     * @Service(name = "_parent", method = "sandboxActionTwo", arguments = {"sum", "this.staff"}, return = "sandbox")
     * @Service(name = "foobar", method = "baz", arguments = {"sandbox"}, return = "_return")
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function bar(int $param1, int $param2);

    /**
     * @Validate(name = "validators", method = "intType", arguments = {"param1"})
     * @Validate(name = "validators", method = "intType", arguments = {"param2"})
     * @Call(name = "validators", method = "sum", arguments = {"param1", "param2"}, return = "sum")
     * @Custom(name = "getSomeStaff", arguments = {"this.param3"}, return = "this.staff")
     * @Service(name = "_parent", method = "sandboxActionTwo", arguments = {"sum", "this.staff"}, return = "sandbox")
     * @Service(name = "foobar", method = "baz", arguments = {"sandbox"}, return = "_return")
     *
     * @param int $param1
     * @param int $param2
     * @return mixed
     */
    public function baz(int $param1, int $param2);
}
