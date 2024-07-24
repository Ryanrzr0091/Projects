import java.util.List;
import java.util.Set;
import java.util.SortedSet;
import java.util.TreeSet;
import java.util.ArrayList;
import java.util.Scanner;
import java.util.Collections;
import java.io.File;
import java.io.IOException;

/**
 * Implements the WordSearchGame interface, providing methods to load a lexicon,
 * set a game board, and search for words on the board.
 */
public class WordSearchGameImpl implements WordSearchGame {
   private SortedSet<String> lexicon;
   private String[] board;
   private int boardSize;
   private boolean lexiconLoaded = false;
   private static final String[] DEFAULT_BOARD = {
      "E", "E", "C", "A", "A", "L", "E", "P", "H",
      "N", "B", "O", "Q", "T", "T", "Y"
   };

   public WordSearchGameImpl() {
      lexicon = new TreeSet<>();
      setBoard(DEFAULT_BOARD);
   }

   @Override
   public void loadLexicon(String fileName) {
      if (fileName == null) {
         throw new IllegalArgumentException("fileName cannot be null");
      }
      lexicon.clear();
      try (Scanner scanner = new Scanner(new File(fileName))) {
         while (scanner.hasNextLine()) {
            lexicon.add(scanner.nextLine().trim().toUpperCase());
         }
      } catch (IOException e) {
         throw new IllegalArgumentException("Error loading lexicon file: " + fileName, e);
      }
      lexiconLoaded = true;
   }

   @Override
   public void setBoard(String[] letterArray) {
      if (letterArray == null || !isSquare(letterArray.length)) {
         throw new IllegalArgumentException("letterArray must be non-null and square");
      }
      board = letterArray;
      boardSize = (int) Math.sqrt(board.length);
   }

   private boolean isSquare(int length) {
      int sqrt = (int) Math.sqrt(length);
      return sqrt * sqrt == length;
   }

   @Override
   public String getBoard() {
      StringBuilder sb = new StringBuilder();
      for (int i = 0; i < boardSize; i++) {
         for (int j = 0; j < boardSize; j++) {
            sb.append(board[i * boardSize + j]).append(" ");
         }
         sb.append("\n");
      }
      return sb.toString();
   }

   @Override
   public SortedSet<String> getAllScorableWords(int minimumWordLength) {
      if (minimumWordLength < 1) {
         throw new IllegalArgumentException("minimumWordLength must be at least 1");
      }
      if (!lexiconLoaded) {
         throw new IllegalStateException("Lexicon must be loaded before calling this method");
      }
   
      SortedSet<String> scorableWords = new TreeSet<>();
      boolean[][] visited = new boolean[boardSize][boardSize];
   
      for (int r = 0; r < boardSize; r++) {
         for (int c = 0; c < boardSize; c++) {
            searchWords(r, c, "", visited, scorableWords, minimumWordLength);
         }
      }
   
      return scorableWords;
   }

   private void searchWords(int row, int col, String prefix, boolean[][] visited, Set<String> scorableWords, int minimumWordLength) {
      if (row < 0 || col < 0 || row >= boardSize || col >= boardSize || visited[row][col]) {
         return;
      }
   
      prefix += board[row * boardSize + col];
      if (!isValidPrefix(prefix)) {
         return;
      }
   
      visited[row][col] = true;
   
      if (prefix.length() >= minimumWordLength && isValidWord(prefix)) {
         scorableWords.add(prefix);
      }
   
      for (int r = -1; r <= 1; r++) {
         for (int c = -1; c <= 1; c++) {
            if (r != 0 || c != 0) {
               searchWords(row + r, col + c, prefix, visited, scorableWords, minimumWordLength);
            }
         }
      }
   
      visited[row][col] = false;
   }

   @Override
   public int getScoreForWords(SortedSet<String> words, int minimumWordLength) {
      if (minimumWordLength < 1) {
         throw new IllegalArgumentException("minimumWordLength must be at least 1");
      }
      if (!lexiconLoaded) {
         throw new IllegalStateException("Lexicon must be loaded before calling this method");
      }
   
      int score = 0;
      for (String word : words) {
         if (isValidWord(word) && word.length() >= minimumWordLength) {
            score += 1 + (word.length() - minimumWordLength);
         }
      }
   
      return score;
   }

   @Override
   public boolean isValidWord(String wordToCheck) {
      if (wordToCheck == null) {
         throw new IllegalArgumentException("wordToCheck cannot be null");
      }
      if (!lexiconLoaded) {
         throw new IllegalStateException("Lexicon must be loaded before calling this method");
      }
   
      return lexicon.contains(wordToCheck.toUpperCase());
   }

   @Override
   public boolean isValidPrefix(String prefixToCheck) {
      if (prefixToCheck == null) {
         throw new IllegalArgumentException("prefixToCheck cannot be null");
      }
      if (!lexiconLoaded) {
         throw new IllegalStateException("Lexicon must be loaded before calling this method");
      }
   
      String upperPrefix = prefixToCheck.toUpperCase();
      String nextPrefix = upperPrefix + Character.MAX_VALUE;
      return !lexicon.subSet(upperPrefix, nextPrefix).isEmpty();
   }

   @Override
   public List<Integer> isOnBoard(String wordToCheck) {
      if (wordToCheck == null) {
         throw new IllegalArgumentException("wordToCheck cannot be null");
      }
      if (!lexiconLoaded) {
         throw new IllegalStateException("Lexicon must be loaded before calling this method");
      }
   
      List<Integer> path = new ArrayList<>();
      boolean[][] visited = new boolean[boardSize][boardSize];
   
      for (int r = 0; r < boardSize; r++) {
         for (int c = 0; c < boardSize; c++) {
            if (searchWordPath(r, c, wordToCheck.toUpperCase(), 0, visited, path)) {
               return path;
            }
         }
      }
   
      return path;
   }

   private boolean searchWordPath(int row, int col, String word, int index, boolean[][] visited, List<Integer> path) {
      if (index == word.length()) {
         return true;
      }
      if (row < 0 || col < 0 || row >= boardSize || col >= boardSize || visited[row][col]) {
         return false;
      }
   
      String current = board[row * boardSize + col];
      if (!word.startsWith(current, index)) {
         return false;
      }
   
      visited[row][col] = true;
      path.add(row * boardSize + col);
   
      for (int r = -1; r <= 1; r++) {
         for (int c = -1; c <= 1; c++) {
            if (r != 0 || c != 0) {
               if (searchWordPath(row + r, col + c, word, index + current.length(), visited, path)) {
                  return true;
               }
            }
         }
      }
   
      visited[row][col] = false;
      path.remove(path.size() - 1);
      return false;
   }
}