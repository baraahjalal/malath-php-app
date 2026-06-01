<?php
namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller {

    public function index(): void   { $this->view('pages.index'); }
    public function about(): void   { $this->view('pages.about'); }
    public function contact(): void { $this->view('pages.contact'); }
    public function faq(): void     { $this->view('pages.faq'); }
    public function privacy(): void { $this->view('pages.privacy'); }
    public function terms(): void   { $this->view('pages.terms'); }
}
