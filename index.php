<?php
require_once 'config/Database.php';
require_once 'models/City.php';

// Initialize database connection
$database = new Database();
$db = $database->connect();

// Initialize city object
$city = new City($db);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $city->name = $_POST['name'];
            $city->description = $_POST['description'];
            $city->create();
        } elseif ($_POST['action'] === 'edit') {
            $city->id = $_POST['id'];
            $city->name = $_POST['name'];
            $city->description = $_POST['description'];
            $city->update();
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Get city details if editing
$currentCity = null;
if (isset($_GET['id'])) {
    $city->id = $_GET['id'];
    if ($city->read_single()) {
        $currentCity = [
            'id' => $city->id,
            'name' => $city->name,
            'description' => $city->description
        ];
    }
}

// Get all cities
$result = $city->read();
$cities = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>City Management</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h2>Cities</h2>
    </header>

    <section>
        <nav>
            <h3>Cities List</h3>
            <ul>
                <?php foreach($cities as $cityItem): ?>
                    <li>
                        <a href="?id=<?php echo $cityItem['id']; ?>" <?php echo (isset($_GET['id']) && $_GET['id'] == $cityItem['id']) ? 'style="font-weight: bold;"' : ''; ?>>
                            <?php echo htmlspecialchars($cityItem['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a href="?add=true" class="btn btn-full">Add New City</a>
        </nav>
        
        <article>
            <?php if($currentCity): ?>
                <?php if(isset($_GET['edit'])): ?>
                    <h3>Edit City</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?php echo $currentCity['id']; ?>">
                        
                        <div class="form-group">
                            <label for="name">City Name:</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($currentCity['name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="6" required><?php 
                                echo htmlspecialchars($currentCity['description']); 
                            ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn">Update City</button>
                        <a href="?id=<?php echo $currentCity['id']; ?>" class="btn">Cancel</a>
                    </form>
                <?php else: ?>
                    <h1><?php echo htmlspecialchars($currentCity['name']); ?></h1>
                    <p><?php echo nl2br(htmlspecialchars($currentCity['description'])); ?></p>
                    <a href="?id=<?php echo $currentCity['id']; ?>&edit=true" class="btn" style="text-decoration: none">Edit City</a>
                <?php endif; ?>
            <?php elseif(isset($_GET['add'])): ?>
                <h3>Add New City</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="name">City Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="6" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn">Add City</button>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn" style="text-decoration: none">Cancel</a>
                </form>
            <?php else: ?>
                <h1>Welcome to City Management</h1>
                <p>Select a city from the list to view its details or click "Add New City" to create a new entry.</p>
            <?php endif; ?>
        </article>
    </section>

    <footer>
        <p>Footer</p>
    </footer>
</body>
</html>

