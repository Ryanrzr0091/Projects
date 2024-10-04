package edu.au.cpsc.module7.data;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;
import edu.au.cpsc.module7.Transaction;
public class Database implements Serializable {
    private static final long serialVersionUID = 1L;
    private final List<Transaction> transactions = new ArrayList<>();
    public void addTransaction(double amount, String category, String date, String type) {
        transactions.add(new Transaction(type, amount, category, date));
    }
    public List<Transaction> getTransactions() {
        return new ArrayList<>(transactions);
    }
    public double getTotalIncome() {
        return transactions.stream()
                .filter(t -> t.getType().equalsIgnoreCase("Income"))
                .mapToDouble(Transaction::getAmount)
                .sum();
    }
    public double getTotalExpense() {
        return transactions.stream()
                .filter(t -> t.getType().equalsIgnoreCase("Expense"))
                .mapToDouble(Transaction::getAmount)
                .sum();
    }
    public void removeTransaction(Transaction transaction) {
        transactions.remove(transaction);
    }
}
