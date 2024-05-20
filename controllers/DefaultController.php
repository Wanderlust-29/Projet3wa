<?php

class DefaultController extends AbstractController
{
    public function home() : void
    {
        $am = new ArticleManager();
        $articles = $am->TopTen();
        $totalPrice = 0;
        $cart = isset($_SESSION["cart"]) ? $_SESSION["cart"] : [];
        dump($articles);
        
        foreach ($cart as $article) {
            if (isset($article['price'])) {
                $totalPrice += $article['price'];
            }
        }

        $this->render("default/home.html.twig", [
            "articles"=>$articles,
            "cart" => $cart,
            "totalPrice" => $totalPrice,
        ]);
    }

    public function notfound() : void
    {
        $this->render("default/404.html.twig", [
        ]);
    }
}