<?php
// 必要なヘルパーファイルをインクルード
require_once('../helpers/GoodsGroupDAO.php');
require_once('../helpers/GoodsDAO.php');
require_once('../helpers/tableNoDAO.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // セッションを開始
}

// DAOオブジェクトの初期化
$goodsGroupADO = new GoodsGroupDAO();
$goodsDAO = new GoodsDAO();
$tableNoDAO = new tableNoDAO();

// 商品グループリストを取得
$groups = $goodsGroupADO->get_goodsgroup();

// MenuDetail.phpから返ってきた場合、セッションからgroupCodeを取得
if (isset($_SESSION['groupCode'])) {
    $groupcode = $_SESSION['groupCode'];
    $products = $goodsDAO->get_goods_by_groupcode($groupcode);
    unset($_SESSION['groupCode']); // セッションのgroupCodeを削除
}
// 商品リストを取得
if (isset($_GET['groupcode'])) {
    $groupcode = $_GET['groupcode']; // クエリパラメータからグループコードを取得
    $products = $goodsDAO->get_goods_by_groupcode($groupcode);
} else {
    // それ以外の場合はおすすめ商品を表示
    $products = $goodsDAO->get_recommend_goods();
}

// スタッフ呼び出し処理（非同期処理用）
if (isset($_POST['call_staff'])) {
    $kaiinID = $_SESSION['kaiinID'] ?? null; // セッションから会員IDを取得
   
    if ($kaiinID) {
        $tableNo = $tableNoDAO->get_kaiin_no_by_id($kaiinID); // tableNo オブジェクトを生成
        $tableNo->yobidasiState = 1; // 状態を呼び出し中（true）に設定
        try {
            $success = $tableNoDAO->update_table_no($tableNo); // データベースに追加
            echo json_encode(["message" => $success ? "スタッフを呼び出しました！" : "呼び出しに失敗しました。"]);
        } catch (Exception $e) {
            echo json_encode(["message" => "データベースエラー: " . htmlspecialchars($e->getMessage())]);
        }
    } else {
        echo json_encode(["message" => "ログインが必要です。"]);
    }
    exit; // JSONレスポンスを返して終了
}
$imageBasePath = "../image/"; // 商品画像のベースパス
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>一目惚れ注文システム - トップページ</title>
    <link rel="stylesheet" href="CSS/test2Style.css">
    <script>
        // 商品画像のベースパスを設定
        const imageBasePath = "../image/";

        // 商品リストデータをPHPから取得してJavaScriptで使用
        const products = <?php echo json_encode($products); ?>;
        const groups = <?php echo json_encode($groups); ?>;

        // 現在表示中のグループコードを保持する変数
        let currentGroupCode = <?php echo isset($groupcode) ? $groupcode : 1; ?>;

        // 商品グループ名を動的にリスト表示する関数
        function showGenre(genre) {
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = '';

            // 現在表示中のグループコードを更新
            currentGroupCode = genre;

            products.forEach(product => {
                if (genre === 'all' || product.groupCode == genre) {
                    const productCard = document.createElement('div');
                    productCard.classList.add('product-card');

                    productCard.innerHTML = `
                        <a href="MenuDetail.php?product_id=${product.goodscode}">
                            <img src="${imageBasePath}${product.img}" alt="${product.name}">
                            <div class="product-info">
                                <h3>${product.name}</h3>
                                <p>価格: ${product.price}円</p>
                            </div>
                        </a>
                    `;
                    mainContent.appendChild(productCard);
                }
            });
        }

        // ページロード時に指定されたグループの商品を表示
        window.onload = function() {
            showGenre(currentGroupCode); // 現在のグループコードに基づいて商品を表示
            showGroupList();
        };

        // 商品グループリストを動的に表示する関数
        function showGroupList() {
            const genreList = document.getElementById('genre-list');
            genreList.innerHTML = '';

            groups.forEach(group => {
                const groupItem = document.createElement('li');
                groupItem.innerHTML = `<a href="javascript:showGenre(${group.groupCode})">${group.groupName}</a>`;
                genreList.appendChild(groupItem);
            });
        }

        // スタッフ呼び出し関数（AJAXを使用）
        function callStaff() {
            if (confirm("スタッフを呼び出しますか？")) {
                fetch('Toppage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'call_staff=1'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                })
                .catch(error => {
                    alert("エラーが発生しました: " + error);
                });
            }
        }
    </script>
</head>
<body>
    <header>
        一目惚れ注文システム
    </header>
    <img src="../image/logo2.png" alt="Logo" class="logo" align="left">
    <nav>
        <div id="link">
            <?php if (isset($_SESSION['kaiin'])) : ?>
                <button onclick="window.location.href = 'LogOut.php';"> ログアウト</button>
            <?php else : ?>
                <button onclick="window.location.href = 'LogiN.php';">ログイン </button>
            <?php endif; ?>
        </div>
        <!-- スタッフ呼び出しボタン（ページ遷移なし） -->
        <button onclick="callStaff();">スタッフを呼び出す</button>
        <button onclick="window.location.href = 'MypagE.php';">マイページ</button>
    </nav>

    <div class="content">
        <div class="left-sidebar">
            <h2>ジャンル</h2>
            <ul class="genre-list" id="genre-list">
                <!-- 商品グループが動的に表示されます -->
            </ul>
        </div>

        <div class="main-content" id="main-content">
            <!-- 動的に商品が表示されます -->
        </div>
    </div>

    <footer>
        <button onclick="window.location.href = 'CarT.php';">カート</button>
        <button onclick="window.location.href = 'OrderHistory.php';">注文履歴</button>
        <button onclick="window.location.href = 'AccountinG.php';">会計</button>
    </footer>
</body>
</html>