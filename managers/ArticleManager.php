<?php

class ArticleManager extends AbstractManager
{
    /**
     * Récupère un article en fonction de son identifiant.
     *
     * @param int $id L'identifiant de l'article à récupérer.
     * @return Article|null L'objet article trouvé ou null s'il n'existe pas.
     */
    public function findOne(int $id): ?Article
    {
        $query = $this->db->prepare('SELECT * FROM articles WHERE id=:id');
        $parameters = ["id" => $id];
        $query->execute($parameters);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $mm = new MediaManager();
            $media = $mm->findOne($result["image_id"]);

            $cm = new CategoryManager();
            $category = $cm->findOne($result["category_id"]);

            $article = new Article($result["name"], $result["price"], $result["stock"], $category, $media, $result["description"], $result["ingredients"], $result["age"]);
            $article->setId($result["id"]);
            return $article;
        }

        return null;
    }

    /**
     * Récupère tous les articles.
     *
     * @return array Liste des articles.
     */
    public function findAll(): array
    {
        $query = $this->db->prepare('SELECT * FROM articles');
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $articles = [];

        foreach ($result as $item) {

            $mm = new MediaManager();
            $media = $mm->findOne($item["image_id"]);

            $cm = new CategoryManager();
            $category = $cm->findOne($item["category_id"]);

            $article = new Article($item["name"], $item["price"], $item["stock"], $category, $media, $item["description"], $item["ingredients"], $item["age"]);
            $article->setId($item["id"]);
            $articles[] = $article;
        }
        return $articles;
    }

    /**
     * Récupère les 9 meilleurs articles basés sur le nombre de ventes.
     *
     * @return array Liste des 9 meilleurs articles.
     */
    public function TopNine(): array
    {
        $query = $this->db->prepare('SELECT articles.*, COUNT(orders_articles.article_id) AS total_sales
                                    FROM articles
                                    JOIN orders_articles ON articles.id = orders_articles.article_id
                                    GROUP BY articles.id
                                    ORDER BY total_sales DESC LIMIT 9;');
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $articles = [];

        foreach ($result as $item) {

            $mm = new MediaManager();
            $media = $mm->findOne($item["image_id"]);

            $cm = new CategoryManager();
            $category = $cm->findOne($item["category_id"]);

            $article = new Article($item["name"], $item["price"], $item["stock"], $category, $media, $item["description"], $item["ingredients"], $item["age"]);
            $article->setId($item["id"]);
            $articles[] = $article;
        }
        return $articles;
    }

    /**
     * Récupère les articles de la categorie Croquettes.
     *
     * @return array Liste des articles de la categorie Croquettes.
     */
    public function findByCat(int $category_id): array
    {
        $query = $this->db->prepare('SELECT * FROM articles WHERE category_id =:category_id');
        $parameters = [
            "category_id" => $category_id
        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $articles = [];

        foreach ($result as $item) {

            $mm = new MediaManager();
            $media = $mm->findOne($item["image_id"]);

            $cm = new CategoryManager();
            $category = $cm->findOne($item["category_id"]);

            $article = new Article($item["name"], $item["price"], $item["stock"], $category, $media, $item["description"], $item["ingredients"], $item["age"]);
            $article->setId($item["id"]);
            $articles[] = $article;
        }
        return $articles;
    }

    /**
     * Met à jour le stock d'un article.
     *
     * @param Article $article L'article à mettre à jour.
     * @return void
     */
    public function update(Article $article): void
    {
        $query = $this->db->prepare('UPDATE articles 
        SET stock = :stock
        WHERE id = :id');

        $parameters = [
            "id" => $article->getId(),
            "stock" => $article->getStock()
        ];
        $query->execute($parameters);
        // Vérifier si l'article a été trouvé et mis à jour
        if ($query->rowCount() === 0) {
            throw new Exception("Article with ID " . $article->getId() . " not found.");
        }
    }

    /**
     * Insère un nouvel article dans la base de données.
     *
     * @param Article $article Le nouvel article à insérer.
     * @return void
     * 
     */
    public function insert(Article $article): void
    {
        $query = $this->db->prepare('INSERT INTO articles (name, price, stock, category_id, image_id, description, ingredients, age) 
            VALUES (:name, :price, :stock, :category_id, :image_id, :description, :ingredients, :age)');

        $parameters = [
            "name" => $article->getName(),
            "price" => $article->getPrice(),
            "stock" => $article->getStock(),
            "category_id" => $article->getCategory()->getId(),
            "image_id" => $article->getImage()->getId(),
            "description" => $article->getDescription(),
            "ingredients" => $article->getIngredients(),
            "age" => $article->getAge()
        ];

        $query->execute($parameters);
        $article->setId($this->db->lastInsertId());
    }

    public function searchArticles($search): array
    {
        $query = $this->db->prepare('SELECT * FROM articles WHERE name LIKE :search OR description LIKE :search OR ingredients LIKE :search');
        $parameters = [
            ':search' => "%$search%"
        ];
        $query->execute($parameters);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        $articles = [];

        foreach ($result as $item) {
            $mm = new MediaManager();
            $media = $mm->findOne($item["image_id"]);

            $cm = new CategoryManager();
            $category = $cm->findOne($item["category_id"]);

            $article = new Article($item["name"], $item["price"], $item["stock"], $category, $media, $item["description"], $item["ingredients"], $item["age"]);
            $article->setId($item["id"]);

            $articles[] = $article;
        }

        return $articles;
    }

    /**
     * Supprime un article.
     *
     * @param Article $article L'article à mettre à jour.
     * @return void
     * @throws Exception Si l'article n'existe pas.
     */
    public function delete(Article $article): void
    {
        $query = $this->db->prepare('DELETE articles 
        WHERE id = :id');

        $parameters = [
            "id" => $article->getId()
        ];
        $query->execute($parameters);
        // Vérifier si l'article a été trouvé et supprimé
        if ($query->rowCount() === 0) {
            throw new Exception("Article with ID " . $article->getId() . " not found.");
        }
    }
}
