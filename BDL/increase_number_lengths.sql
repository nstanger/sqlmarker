ALTER TABLE Staff MODIFY (Salary NUMBER(8,2));

ALTER TABLE Product MODIFY (Stock_Count NUMBER(6),
                            Restock_Level NUMBER(6),
                            Minimum_Level NUMBER(6),
                            List_Price NUMBER(8,2));

ALTER TABLE Component MODIFY (Stock_Count NUMBER(8));

ALTER TABLE Assembly MODIFY (Quantity NUMBER(5));

ALTER TABLE Sale_Line MODIFY (Quantity NUMBER(5),
                              Actual_Price NUMBER(8,2));

ALTER TABLE Order_Line MODIFY (Qty_Ordered NUMBER(6),
                               Price NUMBER(7,2),
                               Qty_Received NUMBER(6));
