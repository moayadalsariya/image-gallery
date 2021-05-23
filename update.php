<?php require_once "./views/paritals/header.php"; ?>

<?php

if (!isset($_GET['id'])) {
    header("Location: ./index.php");
    exit();
}


$id = $_GET['id'];
/** @var \PDO */
require_once "./database.php";
$statment = $pdo->prepare("SELECT * FROM gallery WHERE id=:id;");
$statment->bindValue(':id', $id);
$statment->execute();
$prodcut = $statment->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
function generateRandomString($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST["title"], ENT_QUOTES, "utf-8");
    $desc = htmlspecialchars($_POST["desc"], ENT_QUOTES, "utf-8");
    $image = "";
    if (empty($title)) {
        array_push($errors, "gallery Title is require");
    }
    if (empty($desc)) {
        array_push($errors, "gallery Desc is require");
    }
    // check there is no file uploaded
    if ($_FILES['image']['name'] == '') {
        $image = $prodcut[0]['image'];
    }
    // check if images dir is not exist
    if (!file_exists("./images")) {
        mkdir("./images");
    }
    // check if input filed is selected
    if ($_FILES['image']['name'] != '') {
        if (file_exists($prodcut[0]['image'])) {
            unlink($prodcut[0]['image']);
        }
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $image = "images/" . generateRandomString() . $file_name;
        $image = str_replace(' ', '_', $image);
        $extensions = array("jpeg", "jpg", "png");
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (in_array($ext, $extensions) === false) {
            $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
        }
        if ($file_size > 2097152) {
            $errors[] = 'File size must be excately 2 MB';
        } else {
            move_uploaded_file($file_tmp, $image);
        }
    }
    if (empty($errors)) {
        $statment = $pdo->prepare("UPDATE gallery
        SET title = :title, description = :desc, image = :image
        WHERE id = :id;");
        $statment->bindValue(':title', $title);
        $statment->bindValue(':desc', $desc);
        $statment->bindValue(':image', $image);
        $statment->bindValue(':id', $id);
        $statment->execute();
        header("Location: ./index.php");
        exit();
    }
}



?>
<main>
    <div class="album py-5 bg-light">
        <div class="container mx-auto">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 justify-content-md-center">
                <div class="col">
                    <div class="card shadow-sm">
                        <img src=<?php echo $prodcut[0]["image"]; ?> class="bd-placeholder-img card-img-top" width="100%" alt="" style="object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $prodcut[0]["title"]; ?></h5>
                            <p class="card-text"><?php echo $prodcut[0]["description"]; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><?php echo $prodcut[0]["created_date"]; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container w-50">
        <div class="py-5">

            <?php
            if (!empty($errors)) {
                foreach ($errors as $error) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
            <?php    }
            }
            ?>

            <h3 class="text-center">update image <?php echo $prodcut[0]['title']; ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-floating mb-3">
                    <input type="text" name="title" value="<?php echo $prodcut[0]['title']; ?>" class="form-control" id="floatingInput" placeholder="title">
                    <label for="floatingInput">Title</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="desc" value="<?php echo $prodcut[0]['description']; ?>" class="form-control" id="floatingInput" placeholder="desc">
                    <label for="floatingInput">desc</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="file" name="image" class="form-control" id="floatingInput" placeholder="image">
                    <label for="floatingInput">image</label>
                </div>
                <button type="Submit" class="btn btn-lg btn-success">Submit</button>
            </form>
        </div>
    </div>
</main>

<?php require_once "./views/paritals/footer.php"; ?>