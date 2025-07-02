// تسجيل الدخول باستخدام البريد الإلكتروني وكلمة المرور
document.getElementById('loginForm').addEventListener('submit', function (event) {
    event.preventDefault();
    const email = document.getElementById('emailLogin').value;
    const password = document.getElementById('passwordLogin').value;
    const loginMessage = document.getElementById('loginMessage');

    // تحقق من إدخال البريد الإلكتروني وكلمة المرور
    if (!email || !password) {
        if (loginMessage) {
            loginMessage.innerText = 'يرجى إدخال البريد الإلكتروني وكلمة المرور';
            loginMessage.style.color = 'red';
        }
        return;
    }

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    // إرسال طلب تسجيل الدخول إلى الخادم
    fetch('login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(text => {
            console.log('Raw response:', text); // تتبع الرد الخام في وحدة التحكم
            const data = JSON.parse(text);

            if (data.success) {
                if (data.user_id) sessionStorage.setItem('user_id', data.user_id);
                if (data.role) sessionStorage.setItem('role', data.role);
                if (data.rfid_tag) sessionStorage.setItem('rfid_tag', data.rfid_tag);
                if (loginMessage) {
                    loginMessage.innerText = 'تم تسجيل الدخول بنجاح!';
                    loginMessage.style.color = 'green';
                }
                setTimeout(() => {
                    window.location.href = 'index.php'; // الانتقال إلى الصفحة الرئيسية
                }, 1500);
            } else {
                const errorMessage =
                    data.error === 'Invalid email'
                        ? 'البريد الإلكتروني غير صحيح'
                        : 'كلمة المرور غير صحيحة';

                if (loginMessage) {
                    loginMessage.innerText = errorMessage;
                    loginMessage.style.color = 'red';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (loginMessage) {
                loginMessage.innerText = 'حدث خطأ في الاتصال. حاول مجددًا.';
                loginMessage.style.color = 'red';
            }
        });
});

// تسجيل الدخول باستخدام NFC
document.getElementById('nfcLoginButton').addEventListener('click', async () => {
    const nfcMessage = document.getElementById('nfcMessage');

    if (!nfcMessage) {
        console.error('Element with id "nfcMessage" not found.');
        return;
    }

    if ('NDEFReader' in window) {
        try {
            const ndef = new NDEFReader();
            await ndef.scan();
            nfcMessage.innerText = 'مرر البطاقة فوق الهاتف...';
            ndef.onreading = event => {
                const message = event.message;
                let tagId = null;

                for (const record of message.records) {
                    if (record.recordType === 'text') {
                        const decoder = new TextDecoder();
                        tagId = decoder.decode(record.data);
                        break;
                    }
                }

                if (tagId) {
                    nfcMessage.innerText = `تم قراءة البطاقة: ${tagId}`;
                    nfcLogin(tagId); // إرسال البيانات إلى الخادم
                } else {
                    nfcMessage.innerText = 'لم يتم العثور على بيانات في البطاقة.';
                }
            };
        } catch (error) {
            console.error(error);
            nfcMessage.innerText = 'فشل في قراءة بطاقة NFC. حاول مرة أخرى.';
        }
    } else {
        alert('NFC غير مدعومة في هذا الجهاز');
    }
});

function nfcLogin(tagId) {
    const nfcMessage = document.getElementById('nfcMessage');
    if (!nfcMessage) {
        console.error('Element with id "nfcMessage" not found.');
        return;
    }

    fetch('login2.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rfid_tag: tagId })
    }).then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.user_id) sessionStorage.setItem('user_id', data.user_id);
                if (data.role) sessionStorage.setItem('role', data.role);
                if (data.rfid_tag) sessionStorage.setItem('rfid_tag', data.rfid_tag);
                nfcMessage.innerText = 'تم تسجيل الدخول بنجاح!';
                nfcMessage.style.color = 'green';
                setTimeout(() => {
                    window.location.href = 'index.php'; // الانتقال إلى الصفحة الرئيسية
                }, 1500);
            } else {
                nfcMessage.innerText = 'فشل تسجيل الدخول. البطاقة غير معروفة.';
                nfcMessage.style.color = 'red';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            nfcMessage.innerText = 'حدث خطأ في الاتصال بالخادم. حاول مرة أخرى.';
            nfcMessage.style.color = 'red';
        });
}
