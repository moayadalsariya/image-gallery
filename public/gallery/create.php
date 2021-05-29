<?php require_once "../../views/paritals/header.php"; ?>
<?php
session_start();
/** @var \PDO */
require_once "../../database.php";
require_once "../../utils/functions.php";
$errors = [];

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
        array_push($errors, "gallery Image is require");
    }
    // check if images dir is not exist
    if (!file_exists("../images")) {
        mkdir("../images");
    }
    // check if input filed is selected
    if (isset($_FILES['image'])) {
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $image = "../images/" . generateRandomString() . $file_name;
        $image = str_replace(' ', '_', $image);
        $extensions = array("jpeg", "jpg", "png");
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (in_array($ext, $extensions) === false) {
            $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
        }
        if ($file_size > 2097152) {
            $errors[] = 'File size must less than 2 MB';
        } else {
            move_uploaded_file($file_tmp, $image);
        }
    }


    if (empty($errors)) {
        $statment = $pdo->prepare("INSERT INTO gallery (title,description,image)
        VALUES (:title,:desc,:image);");
        $statment->bindValue(':title', $title);
        $statment->bindValue(':desc', $desc);
        $statment->bindValue(':image', $image);
        $statment->execute();
        $_SESSION['success'] = "you have successful create new post";
        header("Location: ./index.php");
        exit();
    }
    
}
?>

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

        <h3 class="text-center">Upload image</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-floating mb-3">
                <input type="text" name="title" class="form-control" id="floatingInput" placeholder="title">
                <label for="floatingInput">Title</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="desc" class="form-control" id="floatingInput" placeholder="desc">
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



<?php require_once "../../views/paritals/footer.php"; ?>