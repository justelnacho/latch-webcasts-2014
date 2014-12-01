package models;

import com.mongodb.BasicDBObject;
import com.mongodb.DBCollection;
import com.mongodb.DBObject;

public class User {

	private String username;
	private String password;
	private String color;
	private String phone;
	private String accountId;
	
	public User(String username, String password, String color, String phone){
		this.username = username;
		this.password = password;
		this.color = color;
		this.phone = phone;
		this.accountId = "";
	}
	
	private User(DBObject dbUser) {
		this.username = (String) getValueOrDefault(dbUser.get("username"), "");
        this.password = (String) getValueOrDefault(dbUser.get("password"), "");	
        this.color = (String) getValueOrDefault(dbUser.get("color"), "");	
        this.phone = (String) getValueOrDefault(dbUser.get("phone"), "");	
        this.accountId = (String) getValueOrDefault(dbUser.get("accountId"), "");	
	}
	
	public void save() {
        DBCollection usersCollection = MongoFactory.getCollection("users");

        BasicDBObject userValuesDB = new BasicDBObject();
        userValuesDB.append("username", this.username);
        userValuesDB.append("password", this.password);
        userValuesDB.append("color", this.color);
        userValuesDB.append("phone", this.phone);
        userValuesDB.append("accountId", this.accountId);
        
        usersCollection.update(new BasicDBObject("username", this.username), new BasicDBObject("$set", userValuesDB), true, true);
	}
	
	public static boolean authenticate(String username, String password){
		DBCollection usersCollection = MongoFactory.getCollection("users");
        DBObject userDb = usersCollection.findOne(new BasicDBObject("username", username).append("password", password));
        return (userDb != null);
	}
	
	private Object getValueOrDefault(Object value, Object defaultValue) {
        return value == null ? defaultValue : value;
    }
	
	public static User load(String username){
        DBCollection usersCollection = MongoFactory.getCollection("users");

        DBObject userDb = usersCollection.findOne(new BasicDBObject("username", username));

        if (userDb == null) {
            return null;
        }else {
            return new User(userDb);
        }
    }
	
	public void setColor(String color){
		this.color = color;
	}
	
	public void setPhone(String phone){
		this.phone = phone;
	}
	
	public String getPhone(){
		return phone;
	}
	
	public String getColor(){
		return color;
	}
	
	
	public String getAccountId(){
		return accountId;
	}
	
	public void setAccountId(String accountId){
		this.accountId = accountId;
	}
}
