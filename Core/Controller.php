<?php
class Controller
{
    protected $view_base;
    protected $ViewBag = array();
    protected $class_name;
    protected $controller_name;
    protected $requireAuth = false;

    function __construct()
    {
        $this->class_name = get_called_class();
        $this->controller_name = str_replace("Controller", "", $this->class_name);

        $this->ViewBag["title"] = $this->controller_name;
        $this->view_base =  $this->controller_name;

        //print_r(get_class_methods($this));
    }
    public function setTitle($title)
    {
        $this->ViewBag["title"] = $title;
    }
    public function setViewBase($view_base)
    {
        $this->view_base =  $view_base;
    }
    public function _View($view = "index", $data = [])
    {
        //View::renderTemplate($this->view_base . $view . '.php', $this->ViewBag, $data);
        $this->ViewBag['page'] = isset($data[0]) ? $data[0] : '';
        View::render($this->view_base . '/' . $view . '.php', $this->ViewBag, $data);
    }
}
