<?php

if (isset($_REQUEST['Submit'])) {
    $id = $_REQUEST['id'] ?? '';

    // Accept only a positive integer within the supported range.
    $validatedId = is_string($id)
        ? filter_var($id, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
                'max_range' => 2147483647
            ]
        ])
        : false;

    if ($validatedId === false) {
        $html .= '<pre>Invalid User ID. Enter a positive integer.</pre>';
        return;
    }

    $rows = [];

    try {
        switch ($_DVWA['SQLI_DB']) {
            case MYSQL:
                $connection = $GLOBALS['___mysqli_ston'];

                $stmt = mysqli_prepare(
                    $connection,
                    'SELECT first_name, last_name FROM users WHERE user_id = ?'
                );

                if ($stmt === false) {
                    throw new RuntimeException('Query preparation failed.');
                }

                mysqli_stmt_bind_param($stmt, 'i', $validatedId);

                if (!mysqli_stmt_execute($stmt)) {
                    throw new RuntimeException('Query execution failed.');
                }

                mysqli_stmt_bind_result($stmt, $firstName, $lastName);

                while (mysqli_stmt_fetch($stmt)) {
                    $rows[] = [
                        'first_name' => $firstName,
                        'last_name' => $lastName
                    ];
                }

                mysqli_stmt_close($stmt);
                break;

            case SQLITE:
                global $sqlite_db_connection;

                $stmt = $sqlite_db_connection->prepare(
                    'SELECT first_name, last_name FROM users WHERE user_id = :id'
                );

                if ($stmt === false) {
                    throw new RuntimeException('Query preparation failed.');
                }

                $stmt->bindValue(':id', $validatedId, SQLITE3_INTEGER);
                $result = $stmt->execute();

                if ($result === false) {
                    throw new RuntimeException('Query execution failed.');
                }

                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $rows[] = $row;
                }

                $result->finalize();
                $stmt->close();
                break;

            default:
                throw new RuntimeException('Unsupported database.');
        }

        if (count($rows) === 0) {
            $html .= '<pre>User not found.</pre>';
        }

        foreach ($rows as $row) {
            $safeId = htmlspecialchars(
                (string) $validatedId,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $first = htmlspecialchars(
                (string) $row['first_name'],
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $last = htmlspecialchars(
                (string) $row['last_name'],
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $html .= "<pre>ID: {$safeId}<br />First name: {$first}<br />Surname: {$last}</pre>";
        }
    } catch (Throwable $e) {
        error_log('DVWA SQLi lookup failed.');
        $html .= '<pre>Unable to process the request.</pre>';
    }
}
