<?php

if( basename($_SERVER['PHP_SELF']) === basename(__FILE__) ){
    include '../includes/no_access.php';
}

require_once __DIR__.'/bd.php';
require_once __DIR__.'/../classes/User.php';
require_once __DIR__.'/../classes/Quote.php';


/**
 * Connects a user to their profile.
 * 
 * @param mixed $matricule  The user's unique matricule.
 * @param mixed $password   The user's password.
 * 
 * @return User|null        A User object if the credentials match; null otherwise.
 */
function connection_profile_user($matricule, $password) {
    global $pdo;
    $user = null;

    $stmt = $pdo->prepare("SELECT * FROM User WHERE matricule = :matricule;");
    $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
    $stmt->execute();

    if( $stmt->rowCount() > 0 ){
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if( $password === $user_data['password'] ){
            $user = new User(
                $user_data['matricule'],
                $user_data['first_name'],
                $user_data['last_name'],
                $user_data['password'],
                $user_data['avatar_path'],
                $user_data['role']
            );
            return $user;
        }
        return null;
    }
}


/**
 * Creates a new user in the database.
 * 
 * @param mixed $matricule      The user's unique identifier.
 * @param mixed $first_name     The user's first name.
 * @param mixed $last_name      The user's last name.
 * @param mixed $password       The user's password.
 * @param mixed $avatar_path    The user's avatar path.
 * @param mixed $role           The user's role ('admin' or 'user').
 * 
 * @return bool                 True if the user is successfully created, false otherwise.
 */
function create_user($matricule, $first_name, $last_name, $password, $avatar_path, $role){

    global $pdo;

    try{
        $stmt = $pdo->prepare(
            "INSERT INTO User (matricule, first_name, last_name, 
                                      password, avatar_path, role)
                                      VALUES 
                                      (:matricule, :first_name, :last_name,
                                      :password, :avatar_path, :role)"
        );
        return $stmt->execute([
            'matricule' => $matricule,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'password' => $password,
            'avatar_path' => $avatar_path,
            'role' => $role
        ]);

    }catch( PDOException $e ){
        error_log("Erreur SQL : ".$e->getMessage());
        echo "<p style='color:red;'>Erreur lors de l'insertion : ".htmlspecialchars($e->getMessage())."</p>";
        return false;
    }
}


/**
 * Deletes a user from the database and their avatar if is not default avatar.
 * 
 * @param mixed $matricule  The user's unique identifier.
 * 
 * @return bool             True if the user and their avatar (if is not default) are successfully deleted, false otherwise.
 */
function delete_user($matricule) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT avatar_path FROM User WHERE matricule = :matricule;");
    $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if( $user && !empty($user['avatar_path']) && $user['avatar_path'] !== '/avatars/buste.jpg' ){
        $avatar_path = __DIR__ . '/../' . ltrim($user['avatar_path'], '/');  // Adjust path

        if( file_exists($avatar_path) ){
            unlink($avatar_path);  
        }
    }

    $stmt = $pdo->prepare("DELETE FROM User WHERE matricule = :matricule");
    $stmt->bindParam(':matricule', $matricule, PDO::PARAM_STR);
    return $stmt->execute(); 
}


/**
 * Get all users from the database as User objects.
 * 
 * @global PDO $pdo     The PDO database connection object.
 * 
 * @return User[]       An array of User objects. Returns an empty array if no users are found.
 */
function get_all_users() {
    global $pdo;
    $tab_users = [];

    $stmt = $pdo->query("SELECT * FROM User;");

    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    while( $row = $stmt->fetch() ){
        $user = new User();
        $user->hydrate($row);
        $tab_users[] = $user;
    }

    return $tab_users;
}


/**
 * Adds a new quote to the database.
 * 
 * @param mixed $content           The content of the quote.
 * @param mixed $author            The author of the quote.
 * @param mixed $matricule         The ID of the user posting the quote.
 * 
 * @return bool                    True if the quote is successfully added, false otherwise.
 */
function add_quote($content, $author, $matricule){
    global $pdo;

    $stmt = $pdo->prepare(
        "INSERT INTO Quote (content, author, posted_by) 
         VALUES (:content, :author, :posted_by)"
    );

    $success = $stmt->execute([
        'content' => $content,
        'author' => $author,
        'posted_by' => $matricule
    ]);

    return $success;
}


/**
 * Deletes a quote from the database based on its unique ID.
 * 
 * @param int $quote_id The ID of the quote to delete.
 * 
 * @return bool         True if the quote is successfully deleted, false otherwise.
 */
function delete_quote($quote_id) {
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM Quote WHERE quote_id = :quote_id");
    $stmt->bindParam(':quote_id', $quote_id, PDO::PARAM_INT);

    return $stmt->execute();
}


/**
 * Get all quotes from the database.
 * 
 * @global PDO $pdo                 The PDO database connection object.
 * 
 * @param string|null $posted_by    Optional. Fetch quotes by a specific user if provided.
 * 
 * @return Quote[]                   An array of Quote objects. Returns an empty array if no quotes are found.
 */
function get_all_quotes($posted_by = null) {
    global $pdo;
    $quotes = [];
    
    if( $posted_by === null ){
        $sql = "SELECT * FROM Quote;";
        $stmt = $pdo->query($sql);
    }else{
        $sql = "SELECT * FROM Quote WHERE posted_by = :posted_by;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':posted_by', $posted_by, PDO::PARAM_STR);
        $stmt->execute();
    }

    while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
        $quote = new Quote();
        $quote->hydrate($row);
        $quotes[] = $quote;
    }
    return $quotes;
}


/**
 * Get the start and end dates of the week containing a given date or the actual date.
 * 
 * @param string $date A specific date of the week (format: 'Y-m-d').
 * 
 * @return array       An array containing 'start_date' and 'end_date'.
 */
function get_week_period($date=null) {
    if( !$date ){
        $date = date('Y-m-d');
    }

    $dateObj = new DateTime($date);

    $start_date = (clone $dateObj)->modify('monday this week')->format('Y-m-d');
    $end_date = (clone $dateObj)->modify('sunday this week')->format('Y-m-d');

    return ['start_date' => $start_date, 'end_date' => $end_date];
}


/**
 * Get all quotes within a specific date range.
 * 
 * @param mixed $start_date The start date of the range (format: 'Y-m-d').
 * @param mixed $end_date   The end date of the range (format: 'Y-m-d').
 * 
 * @return array            An array of quotes within the date range.
 */
function get_week_quotes($start_date, $end_date, $sort_by='date_desc') {
    global $pdo;

    // Ajouter une condition pour gérer le tri dans la requête SQL
    $sql = "SELECT * FROM Quote WHERE DATE(created_at) BETWEEN :start_date AND :end_date";

    if($sort_by == 'likes_desc'){
        $sql .= " ORDER BY (SELECT COUNT(*) FROM Likes WHERE Likes.quote_id = Quote.quote_id) DESC";
    }else{
        $sql .= " ORDER BY created_at DESC";
    }

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Get the oldest creation date in the `Quote` table.
 * 
 * @global PDO $pdo The PDO database connection object.
 * 
 * @return string|null The oldest date as a string, or null if no records exist.
 */
function get_oldest_date(): ?string {
    global $pdo;

    $sql = "SELECT DATE(MIN(created_at)) AS oldest_date FROM Quote;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['oldest_date'] ?? null;
}


/**
 * Get the most recent creation date in the `Quote` table.
 * 
 * @global PDO $pdo The PDO database connection object.
 * 
 * @return string|null The latest date as a string, or null if no records exist.
 */
function get_latest_date(): ?string {
    global $pdo;

    $sql = "SELECT DATE(MAX(created_at)) AS latest_date FROM Quote;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['latest_date'] ?? null;
}


/**
 * Shifts a given date by one week in the specified direction.
 * 
 * @param mixed $date       The reference date (format: 'Y-m-d').
 * @param mixed $direction  The direction of the shift: 'next' (default) or 'previous'.
 * 
 * @return string           The resulting date in 'Y-m-d' format after the shift.
 */
function shift_week($date, $direction = 'next') {
    $modify = ($direction === 'next') ? '+1 week' : '-1 week';
    return date('Y-m-d', strtotime($modify, strtotime($date)));
}

/**
 * Get the number of likes for a specific quote.
 * 
 * @param mixed $quote_id  The ID of the quote to count likes for.
 * 
 * @return int             The number of likes for the quote.
 */
function get_nb_like($quote_id) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT COUNT(*) AS likes_count FROM Likes WHERE quote_id = :quote_id");
    $stmt->bindParam(':quote_id', $quote_id, PDO::PARAM_INT);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int) $result['likes_count'];
}

/**
 * Fetches quotes from the database, along with their total like count, 
 * and orders them by the number of likes in descending order.
 *
 * This function performs a query that uses a LEFT JOIN to count the likes for each quote,
 * groups the results by quote ID, and sorts them by the total number of likes.
 * 
 * @return array    An array of quotes, each including a 'total_likes' field representing the number of likes.
 */
function get_total_likes_sorted_quotes() {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT q.*, COUNT(l.quote_id) AS total_likes
        FROM Quote q
        LEFT JOIN `Likes` l ON q.quote_id = l.quote_id
        GROUP BY q.quote_id
        ORDER BY total_likes DESC;
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Renders a dynamic button to allow users to like or remove a like from a quote.
 * If the user has already liked the quote, the button will show "Retirer le Like".
 * If the user has not liked the quote, the button will show "Aimer cette citation".
 * 
 * @param mixed $quote_id       The ID of the quote to check for likes.
 * @param mixed $matricule      The unique identifier of the user to check if they have liked the quote.
 * @return string               The HTML markup for the like button, either to like or remove the like from the quote.
 */
function render_like_button($quote_id, $matricule) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT 1 FROM Likes WHERE matricule = :matricule AND quote_id = :quote_id");
    $stmt->execute(['matricule' => $matricule, 'quote_id' => $quote_id]);
    
    $like = $stmt->fetch();

    if( $like ){
        return '<button type="submit" name="action" value="remove_like" class="btn btn-outline-danger">Retirer le Like</button>';
    }else{
        return '<button type="submit" name="action" value="add_like" class="btn btn-outline-danger">Aimer cette citation</button>';
    }
}

function render_follow_button(string $follower, string $followed){

    echo 'première verification '.$follower;
    echo "<br>";
    echo 'deuxiéme verification'.$followed;
    echo "<br>";

    if( is_following($follower, $followed ) ){
        echo 'apparament il ce suivent';
        return '<button type="submit" name="action" value="remove_follow" class="btn btn-outline-danger">retirer le follow</button>';
    }else{
        return '<button type="submit" name="action" value="add_follow" class="btn btn-outline-primary">suivre ce poster</button>';
    }
}


function is_following(string $follower, string $followed): bool {

    echo "<br>";
    echo '1.2 verification '.$follower;
    echo "<br>";
    echo '1.2 verification'.$followed;
    echo "<br>";

    global $pdo;

    $stmt = $pdo->prepare("SELECT 1 FROM Follows WHERE follower = :follower AND followed = :followed LIMIT 1");

    $stmt->execute(['follower' => $follower, 'followed' => $followed]);
    echo (bool) $stmt->fetchColumn();
    return (bool) $stmt->fetchColumn();
}


function add_follow( string $follower, string $followed): void {

    global $pdo;

    echo "je suis dans le add follow";
    echo 'follower vaut'. $follower;
    echo 'followed vaut'. $followed;

    $sql = "INSERT INTO Follows (follower, followed) VALUES (:follower, :followed)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'follower' => $follower,
        'followed' => $followed
    ]);
}


function remove_follow( string $follower, string $followed): void {

    global $pdo;

    $sql = "DELETE FROM Follows WHERE follower = :follower AND followed = :followed";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'follower' => $follower,
        'followed' => $followed
    ]);
}


function get_followed_user_quotes(string $follower): array {

    global $pdo;
    echo " ----> 4";
    $sql = "
        SELECT q.*
        FROM Quote q
        JOIN Follows f ON q.posted_by = f.followed
        WHERE f.follower = :follower
        ORDER BY q.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'follower'   => $follower,
    ]);

    echo " ---->5 ";
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>