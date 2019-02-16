package com.stmvideo.webtv.tv_nome;


import android.app.Activity;
import android.content.Intent;
import android.graphics.PixelFormat;
import android.os.Bundle;
import android.os.Handler;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import com.stmvideo.webtv.tv_nome.R;


public class SplashActivity extends Activity {
	private final Handler mHandler = new Handler();
	private static final int duration = 5000;
	 public void onAttachedToWindow() {
			super.onAttachedToWindow();
			Window window = getWindow();
			window.setFormat(PixelFormat.RGBA_8888);
			
		}
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.splash);
        StartAnimations();
        mHandler.postDelayed(mPendingLauncherRunnable, SplashActivity.duration);
    }
    private void StartAnimations() {
        Animation anim = AnimationUtils.loadAnimation(this, R.anim.from_botton);
        anim.reset();
        ImageView iv = (ImageView) findViewById(R.id.logo);
        iv.clearAnimation();
        iv.startAnimation(anim);
        
        
        
    }
    @Override
	protected void onPause() {
		super.onPause();
		mHandler.removeCallbacks(mPendingLauncherRunnable);
	}

	private final Runnable mPendingLauncherRunnable = new Runnable() {

		public void run() {
			final Intent intent = new Intent(SplashActivity.this,
					MainActivity.class);
			startActivity(intent);
			finish();
		}
	};
   
}