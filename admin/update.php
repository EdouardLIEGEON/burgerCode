<?php 
require 'database.php';

if(!empty($_GET['id'])){
    $id = checkInput($_GET['id']);
}

$nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description = $price = $category = $image = "";

if(!empty($_POST)){
    $name = checkInput($_POST['name']);
    $description = checkInput($_POST['description']);
    $price = checkInput($_POST['price']);
    $category = checkInput($_POST['category']);
    $image = checkInput($_FILES['image']['name']);
    $imagePath = '../img/' . basename($image);
    $imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
    $isSuccess = true;
    

    if(empty($name)){
        $nameError = 'ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if(empty($description)){
        $descriptionError = 'ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if(empty($price)){
        $priceError = 'ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if(empty($category)){
        $categoryError = 'ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if(empty($image)){
        $isImageUpdated = false;
    }else{
        $isImageUpdated = true;
        $isUploadSuccess = true;
        if($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif"){
            $imageError = "Les fichiers autorisés sont : .jpg, .jpeg, .png, .gif";
            $isUploadSuccess = false;
        }
        if(file_exists($imagePath)){
            $imageError = "Le fichier existe déjà";
            $isUploadSuccess = false;
        }
        if($_FILES["image"]["size"] > 500000){
            $imageError = "Le fichier ne doit pas dépasser les 500KB";
            $isUploadSuccess = false;
        }
        if($isUploadSuccess){
            if(!move_upload_files($_FILES["image"]["tmp_name"], $imagePath)){
                $imageError = "Il y a eu une erreur lors de l'upload";
                $isUploadSuccess = false;
            }
        }
    }
    if($isSuccess && isImageUpdated && $isUploadSuccess) || ($isSuccess && !$isImageUpdated){
        $db = Database::connect;
        $statement = $db ->prepare("INSERT INTO items (name, description, price, category, image) values(?, ?, ?, ?, ?)");
        $statement->execute(array($name, $description, $price, $category, $image));
        Database::disconnect();
        header("Location: index.php");
    }
}
function checkInput($data) 
    {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Burger Code</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    
    <body>
        <h1 class="text-logo"><span class="glyphicon glyphicon-cutlery"></span> Burger Code <span class="glyphicon glyphicon-cutlery"></span></h1>
        <div class="container admin">
            <div class="row">
                <div class="col-sm-6">
                <h1><strong>Modifier un item   </strong></h1> <br>
                <form class="form" role="form" action="<?php echo 'update.php?id=' . $id; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nom:</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo $name;?>">
                    <span class="help-inline"><?php echo $nameError; ?></span>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <label for="description">Description:</label>
                    <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?php echo $description; ?>">
                    <span class="help-inline"><?php echo $descriptionError; ?></span>
                </div>
                <div class="form-group">
                    <label for="name">Prix: (en €)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Prix" value="<?php echo $price; ?>">
                    <span class="help-inline"><?php echo $priceError; ?></span>
                </div>
                <div class="form-group">
                    <label for="category">Catégorie:</label>
                    <select class="form-control" id="category" name="category">
                    <?php 
                        $db = Database::connect();
                        foreach($db->query('SELECT * FROM categories') as $row){
                            if($row['id'] == $category)
                                echo '<option selected="selected" value="'. $row['id'] . '">' . $row['name'] . '</option>';
                            else    
                                echo '<option value="'. $row['id'] . '">' . $row['name'] . '</option>';
                        }
                        Database::disconnect();
                    ?>
                    </select>
                    <span class="help-inline"><?php echo $categoryError; ?></span>
                </div>
                <div class="form-group">
                    <label>Image: </label>
                    <p><?php echo $image; ?></p>
                    <label for="image">Sélectionner une image:</label>
                    <input type="file" id="image" name="image">
                    <span class="help-inline"><?php echo $imageError; ?></span>
                </div>
                </div>
            </div>
            <div class="form-actions">
            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span>Modifier</button>
                <a class="btn btn-primary"href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>
            </div>
            </form>
    </body>
</html> 