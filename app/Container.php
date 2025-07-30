<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\Container\ContainerException;
use Psr\Container\ContainerInterface;
use ReflectionUnionType;

class Container implements ContainerInterface
{
    private array $entries=[];
    //!3.5
    public function set(string $id,callable|string $concrete):void
    {
        $this->entries[$id]=$concrete;
    }

    public function get(string $id)
    {

        if($this->has($id)){
            $entry=$this->entries[$id];
            //!3.5  
            if(is_callable($entry)){
                return $entry($this);
            }
            $id=$entry;
        }
       return $this->resolve($id);

    }
    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function resolve(string $id)
    {
        //*  1. Inspect the class that we are trying to getfrom the container
        $reflectionClass =new \ReflectionClass($id);
        if(!$reflectionClass->isInstantiable()){
            throw new ContainerException('Class "'.$id.'" is not instanciable');
        }
        //*  2. Inspect the constructor of the class
        $constructor=$reflectionClass->getConstructor();
        if(!$constructor){
            return new $id;
        }

        //*  3. Inspect the constructor parameters
        $params=$constructor->getParameters();
        if(!$params){
            return new $id;
        }

        //*  4. If the constructor parameter is a class, then try to resolve that class using the container
        $dependencies=array_map(function(\ReflectionParameter $param) use ($id){
            $name=$param->getName();
            $type=$param->getType();

                if(!$type){
                    throw new ContainerException(
                        'Failed to resolve class "'. $id .'" because param "'.$name.'" is missing');
                }
                if($type instanceof ReflectionUnionType){
                    throw new ContainerException(
                        'Failed to resolve class "'. $id .'" because of the union type for param "'.$name.'"');
                }
                if($type instanceof \ReflectionNamedType && ! $type->isBuiltin()){
                    return $this->get($type->getName());
                }
                throw new ContainerException( 
                    'Failed to resolve class "'. $id .'" because of invalid param "'.$name.'"');
            },
            $params
        );

        return $reflectionClass->newInstanceArgs($dependencies);

    }
}
