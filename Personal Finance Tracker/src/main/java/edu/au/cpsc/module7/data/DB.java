package edu.au.cpsc.module7.data;

import java.io.IOException;

public class DB {
    private Database database;
    private DatabaseIO databaseIO;

    public DB() {
        this.database = new Database();
        this.databaseIO = new DatabaseIO();
    }

    public void loadData() throws IOException, ClassNotFoundException {
        database = databaseIO.loadData();
    }

    public void saveData() throws IOException {
        databaseIO.saveData(database);
    }

    public void addIncome(double amount, String category, String date) {
        database.addTransaction(amount, category, date, "Income");
    }

    public void addExpense(double amount, String category, String date) {
        database.addTransaction(amount, category, date, "Expense");
    }

    public double getTotalIncome() {
        return database.getTotalIncome();
    }

    public double getTotalExpenses() {
        return database.getTotalExpense();
    }

    public double getBalance() {
        return getTotalIncome() - getTotalExpenses();
    }
}
