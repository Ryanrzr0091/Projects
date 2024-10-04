package edu.au.cpsc.module7.data;

import java.io.*;

public class DatabaseIO {
    private static final String DATA_FILE = "data.dat";

    public void saveData(Database database) throws IOException {
        try (ObjectOutputStream oos = new ObjectOutputStream(new FileOutputStream(DATA_FILE))) {
            oos.writeObject(database);
        }
    }

    public Database loadData() throws IOException, ClassNotFoundException {
        File file = new File(DATA_FILE);
        if (!file.exists()) {
            return new Database();
        }

        try (ObjectInputStream ois = new ObjectInputStream(new FileInputStream(DATA_FILE))) {
            return (Database) ois.readObject();
        }
    }
}