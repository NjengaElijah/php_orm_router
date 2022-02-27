<?php

class Files
{
    public static function RequireController($file)
    {
        $controllerName  = CONTROLLERS_DIR . ucfirst($file) . '.php';
        //echo $controllerName;
        if (file_exists($controllerName)) {
			//echo "Exist";
           require_once $controllerName;
        } else {
            //throw error
           // throw new Exception("The Controller {$file} at {$controllerName} was not found ");
            //render 404 page
            //View::render('Pages/error.php', ['data' => $file], []);
            printError("Controller not found ... ",$file,404);
            exit;
        }
    }
    public static function RequireModel($file)
    {
        $modelName  = MODELS_DIR . ucfirst($file) . '.php';
        if (file_exists($modelName)) {
            require_once $modelName;
        } else {
            throw new Exception("The Model {$file} at {$modelName} was not found ");
        }
    }
    public static function RequireRepository($file)
    {
        $repoName  = REPOSITORIES_DIR . ucfirst($file) . '.php';
        if (file_exists($repoName)) {
            require_once $repoName;
        } else {
            throw new Exception("The Repository {$file} at {$repoName} was not found ");
        }
    }
}
