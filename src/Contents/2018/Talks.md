<a name="does-the-spl-still-have-any-relevance-in-the-brave-new-world-of-php7"></a>
## Does the SPL still have any relevance in the Brave New World of PHP7?

By [Mark Baker](@baseUrl@/speakers.html#mark-baker)

Largely ignored under PHP5, the SPL (Standard PHP Library) offered a powerful toolbox for developers, ranging from it's horrendously named collection of Iterators, and a series of Interfaces allowing us to build our own, to DataStructures, and Object Oriented file handling classes. Fast and powerful, the SPL provided a stable and well-tested library of classes and functions. But with all the performance and memory improvements of PHP7, SPL has remained unchanged, and feels like it has been left behind. Now, Generators provide a simpler replacement for writing our own Iterators without all the boilerplate code that SPL's core Iterators require, especially with the introduction of "yield from" in PHP7 for recursive Iterators.

And PHP7's performance improvements allow us to write our own Datastructures (based around standard PHP arrays, or custom objects) that are as efficient as SPL's basic Datastructures.

So does SPL still have any purpose or value in this new world of PHP7? Let's find out!

---
