<?php 
    ob_start(); // Start output buffering
    include('partials/menu.php'); 
?>

<div class="main-content">
    <div class="wrapper">
        <h1>Add Food</h1>

        <br><br>

        <?php 
            if(isset($_SESSION['upload'])) {
                echo $_SESSION['upload'];
                unset($_SESSION['upload']);
            }
        ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <table class="tbl-30">
                <tr>
                    <td>Title: </td>
                    <td>
                        <input type="text" name="title" placeholder="Title of the Food">
                    </td>
                </tr>

                <tr>
                    <td>Description: </td>
                    <td>
                        <textarea name="description" cols="30" rows="5" placeholder="Description of the Food."></textarea>
                    </td>
                </tr>

                <tr>
                    <td>Price: </td>
                    <td>
                        <input type="number" name="price">
                    </td>
                </tr>

                <tr>
                    <td>Select Image: </td>
                    <td>
                        <input type="file" name="image">
                    </td>
                </tr>

                <tr>
                    <td>Category: </td>
                    <td>
                        <select name="category">
                            <?php 
                                //Create PHP Code to display categories from Database
                                $sql = "SELECT * FROM tbl_category WHERE active='Yes'";
                                $res = mysqli_query($conn, $sql);
                                $count = mysqli_num_rows($res);

                                if($count > 0) {
                                    // We have categories
                                    while($row = mysqli_fetch_assoc($res)) {
                                        $id = $row['id'];
                                        $title = $row['title'];
                                        echo "<option value='$id'>$title</option>";
                                    }
                                } else {
                                    // No categories found
                                    echo "<option value='0'>No Category Found</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Featured: </td>
                    <td>
                        <input type="radio" name="featured" value="Yes"> Yes 
                        <input type="radio" name="featured" value="No"> No
                    </td>
                </tr>

                <tr>
                    <td>Active: </td>
                    <td>
                        <input type="radio" name="active" value="Yes"> Yes 
                        <input type="radio" name="active" value="No"> No
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <input type="submit" name="submit" value="Add Food" class="btn-secondary">
                    </td>
                </tr>
            </table>
        </form>

        <?php 
            //Check whether the button is clicked or not
            if(isset($_POST['submit'])) {
                // Get the Data from Form
                $title = $_POST['title'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $category = $_POST['category'];

                // Check whether radio button for featured and active are checked or not
                $featured = isset($_POST['featured']) ? $_POST['featured'] : "No";
                $active = isset($_POST['active']) ? $_POST['active'] : "No";

                // Upload the Image if selected
                if(isset($_FILES['image']['name'])) {
                    $image_name = $_FILES['image']['name'];

                    // Check if image is selected
                    if($image_name != "") {
                        // Image is Selected
                        // A. Rename the Image
                        $temp = explode('.', $image_name);
                        $ext = end($temp);
                        $image_name = "Food-Name-" . rand(0000, 9999) . "." . $ext;

                        // B. Upload the Image
                        $src = $_FILES['image']['tmp_name'];
                        $dst = "../images/food/" . $image_name;

                        $upload = move_uploaded_file($src, $dst);

                        // Check whether image uploaded or not
                        if($upload == false) {
                            $_SESSION['upload'] = "<div class='error'>Failed to Upload Image.</div>";
                            header('location:' . SITEURL . 'admin/add-food.php');
                            die();
                        }
                    }
                } else {
                    $image_name = ""; // Setting default value as blank
                }

                // Insert Into Database
                $sql2 = "INSERT INTO tbl_food SET 
                    title = '$title',
                    description = '$description',
                    price = $price,
                    image_name = '$image_name',
                    category_id = $category,
                    featured = '$featured',
                    active = '$active'";

                // Execute the Query
                $res2 = mysqli_query($conn, $sql2);

                // Redirect with message to Manage Food page
                if($res2 == true) {
                    $_SESSION['add'] = "<div class='success'>Food Added Successfully.</div>";
                    header('location:' . SITEURL . 'admin/manage-food.php');
                } else {
                    $_SESSION['add'] = "<div class='error'>Failed to Add Food.</div>";
                    header('location:' . SITEURL . 'admin/manage-food.php');
                }
            }
        ?>
    </div>
</div>

<?php 
    ob_end_flush(); // End output buffering
    include('partials/footer.php'); 
?>
