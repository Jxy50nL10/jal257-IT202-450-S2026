$user_id = get_user_id(); // returns 0 when not logged in

$list_params = array_merge(
    $params,
    [
        "user_id" => $user_id,
    ]
);

$esports = selectAll(
    "SELECT e.id,
            e.api_id,
            e.name,
            e.category_name,
            e.score,
            e.type,
            IF(e.api_id IS NULL, 'Manual', 'API') AS source,
            EXISTS (
                SELECT 1
                FROM UserEsports ue
                WHERE ue.esports_id = e.id
                  AND ue.user_id = :user_id
                  AND ue.is_active = 1
            ) AS is_saved
     FROM Esports e
     $where
     ORDER BY $order_by, e.id ASC
     LIMIT $limit",
    $list_params
);