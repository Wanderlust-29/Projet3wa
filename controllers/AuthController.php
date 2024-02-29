<?php

class AuthController extends AbstractController
{
    public function login() : void
    {
        $nm = new UserManager();
        $mm = new MediaManager();
        $am = new ArticleManager();
        $cm = new CategoryManager();
        $om = new OrderManager();

        $users = $nm->findAll();
        $medias = $mm->findAll();
        $articles = $am->findAll();
        $categories = $cm->findAll();
        $orders = $om->findAll();

        dump($users);
        dump($medias);
        dump($articles);
        dump($categories);
        dump($orders);

        $this->render("login.html.twig", [
            "users"=>$users,
        ]);
    }
}