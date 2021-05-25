<?php require_once "../../views/paritals/header.php"; ?>

<?php
/** @var \PDO */
require_once "../../database.php";

$statment = $pdo->prepare("SELECT * FROM gallery;");
$statment->execute();
$prodcut = $statment->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['q'])) {
  $q = htmlspecialchars($_GET["q"], ENT_QUOTES, "utf-8");

  $statment = $pdo->prepare("SELECT * FROM gallery WHERE title LIKE :title ORDER BY created_date;");
  $statment->bindValue(':title',"%$q%");
  $statment->execute();
  $prodcut = $statment->fetchAll(PDO::FETCH_ASSOC);
}

?>
<main>

  <section class="py-5 text-center container">
    <div class="row py-lg-5">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="fw-light">Image Gallery CRUD</h1>
        <p class="lead text-muted">Something short and leading about the collection below—its contents, the creator, etc. Make it short and sweet, but not too short so folks don’t simply skip over it entirely.</p>
        <p>
          <a href="./create.php" class="btn btn-lg btn-success my-2">Upload Image</a>
        </p>
      </div>
    </div>
  </section>

  <div class="album py-5 bg-light">
    <div class="container">
      <form action="" method="get">
        <div class="input-group mb-3 w-50 mx-auto">
          <input type="text" value="<?php if(isset($q)){echo $q;} ?>" name='q' class="form-control" placeholder="Search for image" aria-label="Username" aria-describedby="basic-addon1">
          <button class="btn-primary">Search</button>
        </div>
      </form>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        <?php foreach ($prodcut as $item) { ?>
          <div class="col">
            <div class="card shadow-sm">
              <img src=<?php echo $item["image"]; ?> class="bd-placeholder-img card-img-top" width="100%" alt="" style="object-fit: cover;">

              <div class="card-body">
                <h5 class="card-title"><?php echo $item["title"]; ?></h5>
                <p class="card-text"><?php echo $item["description"]; ?></p>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <form action="delete.php" method="post">
                      <input type="hidden" name="id" value="<?php echo $item["id"]; ?>">
                      <button type="submit" class="btn btn-danger btn-sm btn-outline-secondary text-white">Delete</button>
                    </form>

                    <a href="update.php?id=<?php echo $item["id"]; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                  </div>
                  <small class="text-muted"><?php echo $item["created_date"]; ?></small>
                </div>
              </div>
            </div>
          </div>
        <?php  }  ?>

      </div>
    </div>
  </div>

</main>


<?php require_once "../../views/paritals/footer.php"; ?>