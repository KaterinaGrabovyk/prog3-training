<?php
 declare(strict_types=1);

 namespace Tests\Unit;

use App\Exceptions\RouteNotFoundException;
use App\Router;
use PHPUnit\Framework\TestCase;

 class RouterTest extends TestCase{
    private Router $router;
    public function setUp():void{
        parent::setUp();
        $this->router=new Router();
    }
    public function test_that_it_registers_a_route():void{
        //given that we have a router object
        // $router =new Router();
        //when we call a register method
        $this->router->register('get','/users',['Users','index']);

        $expected=[
            'get'=>[
                '/users'=>['Users','index']
            ]
        ];
        //then we assert route was registered
        $this->assertSame($expected,$this->router->routes());

    }
    public function test_that_it_registers_a_get_route():void{
        // $router =new Router();

        $this->router->get('/users',['Users','index']);

        $expected=[
            'get'=>[
                '/users'=>['Users','index']
            ]
        ];
        $this->assertSame($expected,$this->router->routes());


    }
    public function test_that_it_registers_a_post_route():void{
        // $router =new Router();

        $this->router->post('/users',['Users','store']);

        $expected=[
            'post'=>[
                '/users'=>['Users','store']
            ]
        ];
        $this->assertSame($expected,$this->router->routes());

    }
    public function test_that_there_are_no_routes_at_beginning():void{
        $this->router=new Router();
        $this->assertEmpty($this->router->routes());
    }
    /**
     * Undocumented function
     * @dataProvider \Tests\DataProviders\RouterDataProvider::RNFCases
     */
    public function test_that_it_throws_route_not_found_ex($reqURI,$reqMethod):void{

        $users=new class(){
           public function delete(){
            return true;
           }
        };

        $this->router->get('/users',['Users','index']);
        $this->router->post('/users',[$users::class,'store']);

        $this->expectException(RouteNotFoundException::class);

        $this->router->resolve($reqURI,$reqMethod);
    }
    public function test_that_it_resolves_route_from_a_cosure():void{
        $this->router->get('/users',fn()=>[1,2,3]);
        $this->assertSame([1,2,3],$this->router->resolve('/users','get'));
    }
    public function test_that_it_resolves_route() :void {
         $users=new class(){
           public function index(){
            return true;
           }
        };       
     $this->router->get('/users',[$users::class,'index']);
     $this->assertSame(true,$this->router->resolve('/users','get'));
    }
 }
