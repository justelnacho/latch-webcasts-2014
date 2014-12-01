package models;

import com.mongodb.DB;
import com.mongodb.DBCollection;
import com.mongodb.Mongo;
import com.mongodb.MongoURI;
import play.Play;

import java.net.UnknownHostException;


public class MongoFactory {

    private static final String MONGO_SCHEME = "mongodb://";
    private static Mongo mongoClient = null;

    public static Mongo createMongoClient() {
        String mongo_uri = Play.configuration.getProperty("mongo_uri");
        mongo_uri = (mongo_uri == null || mongo_uri.isEmpty()) ? null : mongo_uri;
        return createMongoClient(mongo_uri);
    }

    public static synchronized Mongo createMongoClient(String mongoURI) {
        if(MongoFactory.mongoClient == null) {
            try {
                if(mongoURI == null) {
                    MongoFactory.mongoClient = new Mongo();
                } else if (mongoURI.startsWith(MONGO_SCHEME)) {
                    MongoFactory.mongoClient = new Mongo(new MongoURI(mongoURI));
                } else {
                    throw new RuntimeException("Invalid mongo URI: " + mongoURI);
                }
            } catch (UnknownHostException e) {}
        }
        return MongoFactory.mongoClient;
    }

    public static DB getDB() {
        return createMongoClient().getDB("latchtalk");
    }

    public static DBCollection getCollection(String collection) {
        return getDB().getCollection(collection);
    }


}
