package webservicetest;

public class MyRunnable implements Runnable {

	@Override
	public void run() {
		doSomething();
		WebService.result = "finish";
	}
	
	public void doSomething() {
		double i = 0;
		WebService.status = 1; // set start
		while (i < 100) {
			i += 0.0000001;
		}
		WebService.status = 100; // finish
	}

}
