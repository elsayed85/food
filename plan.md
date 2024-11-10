user request order by adding multiple products .. with amount for each
we need to build procuts model , ingredients .. and product ingredients .. Order


Product : 
id , name


Product Seeder : 
id : 1  , name : Burger

Ingredient :
id , name , stock_amount , stock_amount_unit

1 , Beef
2 , Cheese
3 , Onion

recipe : 
product_id , ingredient_id , amount , unit


Order 
product_id , amount , status , created_at , updated_at