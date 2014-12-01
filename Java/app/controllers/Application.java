package controllers;

import models.User;
import play.Logger;
import play.mvc.Controller;

import com.elevenpaths.latch.Latch;
import com.elevenpaths.latch.LatchResponse;

public class Application extends Controller {
	
	private static final String LATCH_APP_ID = "YOUR_APP_ID";
	private static final String LATCH_OP_EDIT_PROFILE = "YOUR_LOGIN_OPERATION_ID";
	private static final String LATCH_OP_LOGIN = "YOUR_EDIT_PROFILE_OPERATION_ID";
	private static final String LATCH_SECRET = "YOUR_APP_SECRET";

    public static void index() {
        render();
    }

    public static void registration() {
        render();
    }
    
    public static void logout() {
    	session.remove("username");
    	index();
    }
    
    public static void doRegister(String username, String password, String color, String phone){
    	User user = new User(username, password, color, phone);
    	user.save();
    	session.put("username", username);
    	profile();
    }
    
    public static void profile() {
    	if (session.contains("username")){
    		String username = session.get("username");
    		User user = User.load(username);
    		render(username, user);
    	}else{
    		index();
    	}
    }
    
    public static void latch(){
    	User user = User.load(session.get("username"));
    	if (user.getAccountId().isEmpty()){
    		render("Latch/pair.html");
    	}else{
    		render("Latch/unpair.html");
    	}
    }
 
    private static void invalidCredentials(){
    	flash.put("errorCredentials", true);
		index();
    }
   
    public static void doLogin(String username, String password){
    	boolean authOk = User.authenticate(username, password);
    	if (authOk){
    		
    		User user = User.load(username);
    		boolean isLoginAllowed = checkLatchStatus(user, LATCH_OP_LOGIN);
    		
    		if(isLoginAllowed){
	    		session.put("username", username);
	    		profile();
    		}else{
    			invalidCredentials();
    		}
    	}else{
    		invalidCredentials();
    	}
    }
    
    public static void editProfile(String color, String phone){
    	User user = User.load(session.get("username"));
    	boolean isEditAllowed = checkLatchStatus(user, LATCH_OP_EDIT_PROFILE);
    	if (isEditAllowed){
	    	user.setColor(color);
	    	user.setPhone(phone);
	    	user.save();
	    	flash.put("editOk", true);
    	}else{
    		flash.put("editFail", true);
    	}
    	profile();
    }
    
    private static boolean checkLatchStatus(User user, String operationId){
    	boolean isAllowed = true;
    	String accountId = user.getAccountId();
    	if (!accountId.isEmpty()){
			Latch latch = new Latch(LATCH_APP_ID,LATCH_SECRET);
	    	LatchResponse latchResponse = latch.operationStatus(accountId, operationId);
	    	if (latchResponse != null && latchResponse.getData() != null){
	    		String status = latchResponse.getData().get("operations").getAsJsonObject().get(operationId).getAsJsonObject().get("status").getAsString();
	    		if(status.equals("off")){
	    			isAllowed = false;
	    		}
	    	}
		}
    	return isAllowed;
    }
    
    public static void pair(String token){
    	Latch latch = new Latch(LATCH_APP_ID,LATCH_SECRET);
    	LatchResponse latchResponse = latch.pair(token);
    	
    	if (latchResponse != null){
    		String json = latchResponse.toJSON().toString();
        	flash.put("json", json);
    		if (latchResponse.getData() != null){
    			String accountId = latchResponse.getData().get("accountId").getAsString();
	    		User user = User.load(session.get("username"));
	    		user.setAccountId(accountId);
	    		user.save();
	    		flash.put("pairOk", true);
    		}else{
        		flash.put("pairFail", true);
        	}
    	}
    	
    	latch();
    }
    
    public static void unpair(){
    	User user = User.load(session.get("username"));
    	Latch latch = new Latch(LATCH_APP_ID,LATCH_SECRET);
    	LatchResponse latchResponse = latch.unpair(user.getAccountId());	
    	if (latchResponse != null && latchResponse.getError() == null){
    		String json = latchResponse.toJSON().toString();
    		flash.put("json", json);
    		
    		user.setAccountId("");
    		user.save();
    		flash.put("unpairOk", true);
    	}else{
    		flash.put("unpairFail", true);
    	}
    	
    	latch();
    }
}