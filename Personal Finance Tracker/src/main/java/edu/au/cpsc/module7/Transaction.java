package edu.au.cpsc.module7;

import java.io.Serializable;

public class Transaction implements Serializable {
    private String type;
    private double amount;
    private String category;
    private String date;

    public Transaction(String type, double amount, String category, String date) {
        this.type = type;
        this.amount = amount;
        this.category = category;
        this.date = date;
    }

    public String getType() {
        return type;
    }

    public double getAmount() {
        return amount;
    }

    public String getCategory() {
        return category;
    }

    public String getDate() {
        return date;
    }
}