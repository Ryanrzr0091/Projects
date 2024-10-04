package edu.au.cpsc.module7;

import edu.au.cpsc.module7.data.Database;
import edu.au.cpsc.module7.Transaction;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.VBox;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.scene.input.MouseEvent;

import java.io.*;
import java.time.LocalDate;
import java.util.List;

public class FinanceController {

    @FXML
    private TextField incomeField;
    @FXML
    private ComboBox<String> incomeCategoryComboBox;
    @FXML
    private TextField expenseField;
    @FXML
    private ComboBox<String> expenseCategoryComboBox;

    @FXML
    private Label totalIncomeLabel;
    @FXML
    private Label totalExpenseLabel;
    @FXML
    private Label balanceLabel;

    @FXML
    private TableView<Transaction> transactionsTableView;
    @FXML
    private TableColumn<Transaction, String> typeColumn;
    @FXML
    private TableColumn<Transaction, Double> amountColumn;
    @FXML
    private TableColumn<Transaction, String> categoryColumn;
    @FXML
    private TableColumn<Transaction, String> dateColumn;

    private Database database = new Database();

    @FXML
    public void initialize() {
        incomeCategoryComboBox.setItems(FXCollections.observableArrayList("Primary Job", "Rental Properties", "YouTube Channel"));
        expenseCategoryComboBox.setItems(FXCollections.observableArrayList("Mortgage", "Power", "Water", "Auto", "Groceries", "Entertainment", "Education", "Personal", "Medical"));

        typeColumn.setCellValueFactory(new PropertyValueFactory<>("type"));
        amountColumn.setCellValueFactory(new PropertyValueFactory<>("amount"));
        categoryColumn.setCellValueFactory(new PropertyValueFactory<>("category"));
        dateColumn.setCellValueFactory(new PropertyValueFactory<>("date"));
        updateTableView();
        updateSummary();
    }

    @FXML
    public void addIncome() {
        String incomeText = incomeField.getText();
        if (incomeText != null && !incomeText.isEmpty() && incomeCategoryComboBox.getValue() != null) {
            try {
                double incomeAmount = Double.parseDouble(incomeText);
                database.addTransaction(incomeAmount, incomeCategoryComboBox.getValue(), LocalDate.now().toString(), "Income");
                updateTableView();
                updateSummary();
                incomeField.clear();
            } catch (NumberFormatException e) {
                showAlert("Invalid Input", "Please enter a valid number for income.");
            }
        } else {
            showAlert("Missing Information", "Please enter an amount and select a category.");
        }
    }

    @FXML
    public void addExpense() {
        String expenseText = expenseField.getText();
        if (expenseText != null && !expenseText.isEmpty() && expenseCategoryComboBox.getValue() != null) {
            try {
                double expenseAmount = Double.parseDouble(expenseText);
                database.addTransaction(expenseAmount, expenseCategoryComboBox.getValue(), LocalDate.now().toString(), "Expense");
                updateTableView();
                updateSummary();
                expenseField.clear();
            } catch (NumberFormatException e) {
                showAlert("Invalid Input", "Please enter a valid number for expense.");
            }
        } else {
            showAlert("Missing Information", "Please enter an amount and select a category.");
        }
    }

    private void updateTableView() {
        List<Transaction> dbTransactions = database.getTransactions();
        ObservableList<Transaction> observableTransactions = FXCollections.observableArrayList(dbTransactions);
        transactionsTableView.setItems(observableTransactions);
    }

    @FXML
    public void saveData() {
        try (ObjectOutputStream oos = new ObjectOutputStream(new FileOutputStream("data.dat"))) {
            oos.writeObject(database.getTransactions());
            showAlert("Success", "Data saved successfully!");
        } catch (IOException e) {
            showAlert("Error", "Failed to save data.");
        }
    }

    @FXML
    public void loadData() {
        try (ObjectInputStream ois = new ObjectInputStream(new FileInputStream("data.dat"))) {
            List<Transaction> loadedTransactions = (List<Transaction>) ois.readObject();
            for (Transaction transaction : loadedTransactions) {
                database.addTransaction(transaction.getAmount(), transaction.getCategory(), transaction.getDate(), transaction.getType());
            }
            updateTableView();
            updateSummary();
            showAlert("Success", "Data loaded successfully!");
        } catch (IOException | ClassNotFoundException e) {
            showAlert("Error", "Failed to load data.");
        }
    }

    @FXML
    public void showTotals() {
        Stage totalsStage = new Stage();
        totalsStage.setTitle("Income and Expense Totals");
        totalsStage.initModality(Modality.WINDOW_MODAL);
        totalsStage.initOwner(totalIncomeLabel.getScene().getWindow());

        VBox vbox = new VBox(10);
        vbox.getChildren().addAll(
                new Label(String.format("Total Income: $%.2f", database.getTotalIncome())),
                new Label(String.format("Total Expense: $%.2f", database.getTotalExpense()))
        );

        Scene scene = new Scene(vbox, 300, 150);
        totalsStage.setScene(scene);
        totalsStage.show();
    }

    @FXML
    public void deleteTransaction() {
        Transaction selectedTransaction = transactionsTableView.getSelectionModel().getSelectedItem();
        if (selectedTransaction != null) {
            database.removeTransaction(selectedTransaction);
            updateTableView();
            updateSummary();
        } else {
            showAlert("No Selection", "Please select a transaction to delete.");
        }
    }

    @FXML
    public void selectTransaction(MouseEvent event) {
        transactionsTableView.getSelectionModel().select(transactionsTableView.getSelectionModel().getSelectedIndex());
    }

    private void updateSummary() {
        double totalIncome = database.getTotalIncome();
        double totalExpense = database.getTotalExpense();
        double balance = totalIncome - totalExpense;

        totalIncomeLabel.setText(String.format("Total Income: $%.2f", totalIncome));
        totalExpenseLabel.setText(String.format("Total Expense: $%.2f", totalExpense));
        balanceLabel.setText(String.format("Balance: $%.2f", balance));
    }

    private void showAlert(String title, String message) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}