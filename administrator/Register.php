<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規登録 - 情報入力</title>
    <link rel="stylesheet" href="CSS/RegisterStyle.css">
    <script>
        // フォームの検証関数
        function validateForm(event) {
            const phoneNumber = document.getElementById('phoneNumber');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const lastName = document.getElementById('lastName');
            const firstName = document.getElementById('firstName');
            let isValid = true;

            document.querySelectorAll('.error-message').forEach(e => e.style.display = 'none');

            const phonePattern = /^\d{2,4}-\d{2,4}-\d{4}$/;
            if (!phonePattern.test(phoneNumber.value)) {
                document.getElementById('phoneError').style.display = 'block';
                isValid = false;
            }

            if (!email.checkValidity()) {
                document.getElementById('emailError').style.display = 'block';
                isValid = false;
            }

            if (password.value.length < 8) {
                document.getElementById('passwordError').style.display = 'block';
                isValid = false;
            }

            const namePattern = /^[ぁ-んァ-ヶ一-龯]+$/;
            if (!namePattern.test(lastName.value)) {
                document.getElementById('lastNameError').style.display = 'block';
                isValid = false;
            }

            if (!namePattern.test(firstName.value)) {
                document.getElementById('firstNameError').style.display = 'block';
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            } else {
                const confirmation = confirm("本当に送信しますか？");
                if (!confirmation) {
                    event.preventDefault();
                }
            }
        }
    </script>
</head>
<body>
    <h1>新規登録 - 情報入力</h1>
    <form 
        id="editForm" 
        class="container" 
        action="RegistrationConfirmation.php" 
        method="POST" 
        onsubmit="validateForm(event);"
    >
        <div class="section">
            <h2>連絡情報</h2>
            <div class="form-group">
                <label for="phoneNumber">電話番号</label>
                <input 
                    type="text" 
                    id="phoneNumber" 
                    name="phoneNumber" 
                    placeholder="例: 090-1234-5678" 
                    required 
                    pattern="^\d{2,4}-\d{2,4}-\d{4}$"
                >
                <div class="error-message" id="phoneError">電話番号の形式が正しくありません。</div>
            </div>

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="メールアドレスを入力してください" 
                    required
                >
                <div class="error-message" id="emailError">正しいメールアドレスを入力してください。</div>
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="8文字以上を入力してください" 
                    required 
                    pattern=".{8,}"
                >
                <div class="error-message" id="passwordError">パスワードは最低8文字以上で入力してください。</div>
            </div>
        </div>

        <div class="section">
            <h2>お客様情報</h2>
            <div class="form-group">
                <label for="lastName">姓</label>
                <input 
                    type="text" 
                    id="lastName" 
                    name="lastName" 
                    placeholder="姓を入力してください" 
                    required 
                    pattern="^[ぁ-んァ-ヶ一-龯]+$"
                >
                <div class="error-message" id="lastNameError">姓は漢字、ひらがな、カタカナで入力してください。</div>
            </div>

            <div class="form-group">
                <label for="firstName">名</label>
                <input 
                    type="text" 
                    id="firstName" 
                    name="firstName" 
                    placeholder="名を入力してください" 
                    required 
                    pattern="^[ぁ-んァ-ヶ一-龯]+$"
                >
                <div class="error-message" id="firstNameError">名は漢字、ひらがな、カタカナで入力してください。</div>
            </div>
        </div>

        <div class="button-container">
            <button type="submit" class="confirm-btn">確認</button>
            <button type="button" class="cancel-btn" onclick="location.href='Mypage.php'">戻る</button>
        </div>
    </form>
</body>
</html>
