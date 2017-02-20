# Model

A powerful database model for web-artisans.

**Implements:** [\IteratorAggregate](http://php.net/manual/en/class.iteratoraggregate.php),
                [\Countable](http://php.net/manual/en/class.countable.php),
                [\ArrayAccess](http://php.net/manual/en/class.arrayaccess.php)

# Table of Contents:

List of **magic methods**:

* [__sleep](#__sleep)
* [__wakeup](#__wakeup)

* [driver](#driver)
* [hasMany](#hasMany)
* [belongsTo](#belongsTo)
* **Query Builder**
    * **Queries**
        * **Select**
        * **Update**
        * **Delete**
        * **Insert**
    * **Clauses**
        * **From**
        * **Join**
        * **Where**
        * **Group By**
        * **Having**
        * **Order By**
        * **Limit**
        * **Offset**
* **Table**
* **Row**
    * [row](#row)
    * [rowOrFail](#rowOrFail)
