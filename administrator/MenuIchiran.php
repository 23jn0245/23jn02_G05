<?php
require_once('../helpers/GoodsDAO.php');
require_once('../helpers/GoodsGroupDAO.php');

$goodsGroupADO = new GoodsGroupDAO(); // 商品グループDAOオブジェクト
$groups = $goodsGroupADO->get_goodsgroup(); // 商品グループリストを取得
$goodsDAO = new GoodsDAO(); // 商品DAOオブジェクト

$searchKeyword = $_GET['search'] ?? ''; // 検索キーワードを取得
if (!empty($searchKeyword)) {
    // 商品名で検索
    $products = $goodsDAO->search_goods_by_name($searchKeyword);
} else if (isset($_GET['groupcode'])) {
    // 商品グループコードがある場合はそのグループの商品を取得
    $groupcode = $_GET['groupcode']; // クエリパラメータからグループコードを取得
    $products = $goodsDAO->get_goods_by_groupcode($groupcode); // 選択されたグループの商品を取得
} else {
    $products = $goodsDAO->get_recommend_goods(); // おすすめ商品を取得
}

$imageBasePath = "../image/"; // 商品画像のベースパス

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['goodsCode'])) {
    $goodsCode = (int)$_POST['goodsCode']; // 削除する商品のコードを取得
    $result = $goodsDAO->delete_goods_by_code($goodsCode); // 商品削除メソッドを呼び出し

    if ($result) {
        // 削除成功時
        echo "<script>alert('商品を削除しました。'); location.href='MenuIchiran.php';</script>";
        exit;
    } else {
        // 削除失敗時
        echo "<script>alert('商品の削除に失敗しました。');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>メニュー一覧</title>
    <link rel="stylesheet" href="CSS/MenuIchiranStyle.css">
    <style>
        /* スクロール可能なテーブルスタイル */
        .scrollable-table {
            max-height: 300px;
            overflow-y: scroll;
            display: block;
        }

        .scrollable-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .scrollable-table th,
        .scrollable-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .scrollable-table th {
            position: sticky;
            top: 0;
            background-color: #f9f9f9;
            z-index: 2;
        }

        .add-button {
            background-color: #d32f2f;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            right: 20px; 
            bottom: 300px; 
            transform: translateY(968%);
            width: 70px;
            height : 45px;
        }
    </style>
    <script>
        function confirmDelete(form) {
            if (confirm('本当に削除しますか？')) {
                form.submit();
            }
        }
    </script>
</head>

<body>
    <header>
        <img src="../image/logo2.png" alt="ロゴ画像" class="logo">
        <h1>メニュー一覧</h1>
    </header>

    <!-- 検索フォーム -->
    <form method="GET" action="MenuIchiran.php">
        <label for="search">商品名で検索:</label>
        <input type="text" id="search" name="search" placeholder="商品名を入力" value="<?= htmlspecialchars($searchKeyword) ?>">
        <button type="submit">検索</button>
        <button type="button" onclick="location.href='MenuIchiran.php'">リセット</button>
    </form>

    <div class="scrollable-table">
        <table>
            <thead>
                <tr>
                    <th>商品名</th>
                    <th>写真</th>
                    <th>説明</th>
                    <th>ジャンル名&番号</th>
                    <th>単価</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product->name) ?></td>
                            <td><img src="<?= htmlspecialchars($imageBasePath . $product->img) ?>" alt="<?= htmlspecialchars($product->name) ?>" style="width: 50px;"></td>
                            <td><?= htmlspecialchars($product->detail ?? '説明なし') ?></td>
                            <!-- グループ名の取得時にプロパティを指定 -->
                            <td>
                                <?php 
                                $group = $goodsGroupADO->get_goodsname($product->groupCode);
                                echo htmlspecialchars($group ? "{$group->groupName} ({$product->groupCode})" : 'グループなし');
                                ?>
                            </td>
                            <td>¥<?= htmlspecialchars($product->price) ?></td>
                            <td>
                                <div class="button-group">
                                    <form action="MenuEdit.php" method="get" style="display: inline;">
                                        <input type="hidden" name="goodsCode" value="<?= htmlspecialchars($product->goodscode) ?>">
                                        <input type="hidden" name="name" value="<?= htmlspecialchars($product->name) ?>">
                                        <input type="hidden" name="price" value="<?= htmlspecialchars($product->price) ?>">
                                        <input type="hidden" name="detail" value="<?= htmlspecialchars($product->detail) ?>">
                                        <input type="hidden" name="groupCode" value="<?= htmlspecialchars($product->groupCode) ?>">
                                        <input type="hidden" name="img" value="<?= htmlspecialchars($product->img) ?>">
                                        <button type="submit">変更</button>
                                    </form>
                                    <form action="MenuIchiran.php" method="post" style="display: inline;">
                                        <input type="hidden" name="goodsCode" value="<?= htmlspecialchars($product->goodscode) ?>">
                                        <button type="button" onclick="confirmDelete(this.form)">削除</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">商品情報がありません。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- 追加ボタン -->
    <button type="button" onclick="location.href='toppage.php'">戻る</button>
    <button type="button" class="add-button" onclick="location.href='AddMenu.php'">追加</button>
</body>

</html>
