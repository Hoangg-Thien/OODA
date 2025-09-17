package DAO;

import java.util.ArrayList;

public interface chitietInterface<T> {
    public int insert(ArrayList<T> t);
    public int update(ArrayList<T> t, String pk);
    public int delete(String t);
    public ArrayList<T> selectAll(String t);
}
