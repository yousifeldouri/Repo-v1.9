package sd.bashaer.rebo

import android.annotation.SuppressLint
import android.os.Build
import android.os.Bundle
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.Toast
import android.util.Log
import android.webkit.WebResourceResponse
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Scaffold
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.viewinterop.AndroidView
import sd.bashaer.rebo.ui.theme.ReboTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            ReboTheme {
                AppContent()
            }
        }
    }
}

@Composable
fun AppContent() {
    Scaffold(modifier = Modifier.fillMaxSize()) { innerPadding ->
        WebViewComponent(modifier = Modifier.padding(innerPadding))
    }
}

@SuppressLint("SetJavaScriptEnabled")
@Composable
fun WebViewComponent(modifier: Modifier = Modifier) {
    AndroidView(
        factory = { context ->
            WebView(context).apply {
                settings.apply {
                    javaScriptEnabled = true
                    domStorageEnabled = true
                    useWideViewPort = true
                    loadWithOverviewMode = true
                    allowFileAccess = true
                    databaseEnabled = true
                    setSupportZoom(true)
                    builtInZoomControls = true
                    displayZoomControls = false
                    loadsImagesAutomatically = true  // تمكين تحميل الصور تلقائيًا

                    mixedContentMode = WebSettings.MIXED_CONTENT_COMPATIBILITY_MODE

                    // تغيير الكاش
                    cacheMode = WebSettings.LOAD_DEFAULT
                }

                webViewClient = object : WebViewClient() {
                    override fun onReceivedError(
                        view: WebView,
                        request: WebResourceRequest,
                        error: WebResourceError
                    ) {
                        Log.e("WebViewError", "Error: ${error.description}")
                        Toast.makeText(context, "فشل في تحميل الصفحة", Toast.LENGTH_SHORT).show()
                    }

                    override fun onReceivedHttpError(
                        view: WebView,
                        request: WebResourceRequest,
                        errorResponse: WebResourceResponse
                    ) {
                        Log.e("WebViewHttpError", "HTTP Error: ${errorResponse.statusCode}")
                    }
                }

                loadUrl("http://192.168.88.74/new%20folder/finch/login.html")
            }
        },
        modifier = modifier.fillMaxSize()
    )
}
