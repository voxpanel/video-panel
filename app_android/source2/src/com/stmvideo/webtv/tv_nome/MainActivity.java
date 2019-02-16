package com.stmvideo.webtv.tv_nome;


import com.stmvideo.webtv.tv_nome.R;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebChromeClient;
import android.webkit.WebChromeClient.CustomViewCallback;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.webkit.WebSettings.PluginState;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;

public class MainActivity extends Activity {

    WebView mWebView;
  
  String URL= "file:///android_asset/index.html";

  ProgressBar loadingProgressBar,loadingTitle;
  
  @Override
   public void onCreate(Bundle savedInstanceState) {
   super.onCreate(savedInstanceState);
   setContentView(R.layout.activity_main);
   
   mWebView = (WebView) findViewById(R.id.webview);
   mWebView.getSettings().setJavaScriptCanOpenWindowsAutomatically(true);
   mWebView.getSettings().setPluginState(PluginState.ON);
   mWebView.getSettings().setJavaScriptEnabled(true);
   mWebView.getSettings().setJavaScriptEnabled(true);
   mWebView.setLayoutParams(new LinearLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT));
   mWebView.clearCache(true);
   mWebView.loadUrl(URL);
   mWebView.setWebViewClient(new MyWebViewClient());
   
   loadingProgressBar=(ProgressBar)findViewById(R.id.progressbar_Horizontal); 
   
   mWebView.setWebChromeClient(new WebChromeClient() {

   // this will be called on page loading progress

   @Override
   public void onProgressChanged(WebView view, int newProgress) {

   super.onProgressChanged(view, newProgress);


   loadingProgressBar.setProgress(newProgress);
   //loadingTitle.setProgress(newProgress);
   // hide the progress bar if the loading is complete

   if (newProgress == 100) {
   loadingProgressBar.setVisibility(View.VISIBLE);
   
   } else{
   loadingProgressBar.setVisibility(View.VISIBLE);
   
   }

   }

   });

   }
 
 private class MyWebViewClient extends WebViewClient {

 
 @Override
 public boolean shouldOverrideUrlLoading(WebView view, String url) {

 view.loadUrl(url);
 return true;
 }
 }
 
 
 
 @Override
 public boolean onCreateOptionsMenu(Menu menu) {
 	getMenuInflater().inflate(R.menu.main, menu);
 	return true;
 }

 @Override
 public boolean onOptionsItemSelected(MenuItem item) {
 	switch (item.getItemId()) {
 	case R.id.reload:
 		
 		mWebView.loadUrl( "javascript:window.location.reload( true )" );   
 		
 		return true;
 		
 		
 	case R.id.exit:
 		

 		String message = "MSGSAIR";

 		AlertDialog.Builder ad = new AlertDialog.Builder(this);
 		ad.setTitle("Oops!");
 		ad.setMessage(message);
 		ad.setCancelable(true);
 		ad.setPositiveButton("MSGSIM",
 				new DialogInterface.OnClickListener() {
 					@Override
 					public void onClick(DialogInterface dialog, int which) {

 							finish();
 					}
 				});

 		ad.setNegativeButton("MSGCANCELAR", null);

 		ad.show();

 		return true;
 		
 		
     }
     return super.onOptionsItemSelected(item);
 		
 	
 	}

 @Override
 public void onBackPressed()
 {
     if(mWebView.canGoBack())
    	 mWebView.goBack();
     else
         super.onBackPressed();
 }
 
}
