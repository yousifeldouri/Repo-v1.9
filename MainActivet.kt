package sd.bashaer.rebo

import android.app.Activity
import android.content.Intent
import android.nfc.NfcAdapter
import android.nfc.NfcEvent
import android.os.Build
import android.os.Bundle
import android.widget.Toast
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.viewinterop.AndroidView
import sd.bashaer.rebo.ui.theme.ReboTheme

class MainActivity : ComponentActivity() {
    private lateinit var nfcAdapter: NfcAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            ReboTheme {
                Scaffold(modifier = Modifier.fillMaxSize()) { innerPadding ->
                    WebViewComponent(modifier = Modifier.padding(innerPadding))
                }
            }
        }

        // التحقق من دعم NFC
        nfcAdapter = NfcAdapter.getDefaultAdapter(this)
        if (nfcAdapter == null) {
            Toast.makeText(this, "NFC غير مدعوم على هذا الجهاز", Toast.LENGTH_SHORT).show()
            finish()  // إنهاء التطبيق إذا كان الجهاز لا يدعم NFC
        }
    }

    override fun onResume() {
        super.onResume()
        if (NfcAdapter.ACTION_TAG_DISCOVERED == intent.action) {
            processNfcData(intent)
        }
    }

    override fun onNewIntent(intent: Intent) {
        super.onNewIntent(intent)
        if (NfcAdapter.ACTION_TAG_DISCOVERED == intent.action) {
            processNfcData(intent)
        }
    }

    private fun processNfcData(intent: Intent) {
        // استخراج البيانات من الـ Intent
        val tag = intent.getParcelableExtra<android.nfc.Tag>(NfcAdapter.EXTRA_TAG)
        if (tag != null) {
            val tagId = tag.id.joinToString(":") { it.toString(16) }
            Toast.makeText(this, "تم العثور على بطاقة: $tagId", Toast.LENGTH_LONG).show()
            // هنا يمكنك إرسال البيانات إلى الخادم باستخدام POST أو تنفيذ إجراء آخر
        }
    }
}

@Composable
fun WebViewComponent(modifier: Modifier = Modifier) {
    AndroidView(
        factory = { context ->
            WebView(context).apply {
                settings.javaScriptEnabled = true

                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                    settings.mixedContentMode = WebSettings.MIXED_CONTENT_COMPATIBILITY_MODE
                }

                settings.cacheMode = WebSettings.LOAD_DEFAULT
                webViewClient = WebViewClient()
                loadUrl("http://192.168.88.74/new%20folder/finch/login.html") // استبدلها بعنوان URL الفعلي
            }
        },
        modifier = modifier.fillMaxSize()
    )
}
