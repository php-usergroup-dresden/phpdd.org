<a name="does-the-spl-still-have-any-relevance-in-the-brave-new-world-of-php7"></a>
## Does the SPL still have any relevance in the Brave New World of PHP7?

By [Mark Baker](@baseUrl@/speakers.html#mark-baker)

Largely ignored under PHP5, the SPL (Standard PHP Library) offered a powerful toolbox for developers, ranging from it's horrendously named collection of Iterators, and a series of Interfaces allowing us to build our own, to DataStructures, and Object Oriented file handling classes. Fast and powerful, the SPL provided a stable and well-tested library of classes and functions. But with all the performance and memory improvements of PHP7, SPL has remained unchanged, and feels like it has been left behind. Now, Generators provide a simpler replacement for writing our own Iterators without all the boilerplate code that SPL's core Iterators require, especially with the introduction of "yield from" in PHP7 for recursive Iterators.

And PHP7's performance improvements allow us to write our own Datastructures (based around standard PHP arrays, or custom objects) that are as efficient as SPL's basic Datastructures.

So does SPL still have any purpose or value in this new world of PHP7? Let's find out!

---

<a name="getting-started-with-kubernetes"></a>
## Getting started with Kubernetes

By [Bastian Hofmann](@baseUrl@/speakers.html#bastian-hofmann)

Kubernetes is a very powerful container orchestration platform that is quickly gaining traction and gives you lots of 
benefits in deploying, running and scaling your microservice web application. But it has also a steep learning curve. 

In this talk I will introduce you to Kubernetes, why you would want to use it and all the tooling around Kubernetes 
with the help of practical examples.

---

<a name="zero-downtime-database-migrations-and-deployments"></a>
## Zero Downtime Database Migrations and Deployments

By [Ondřej Mirtes](@baseUrl@/speakers.html#ondrej-mirtes)

To survive in a competitive market, a software team must be able to deploy new versions of their application as frequently as possible, delivering new features, improvements and bugfixes for their users and stakeholders. Deployments should not be limited to a certain time or to a certain number, otherwise they become a bottleneck for the development process.

To avoid disruptions when frequently deploying new versions, developers must adopt a set of practices and workflows that allow changing the database schema in production without users even noticing. As a result, the development process becomes much more safe and smooth.

I will also talk about zero downtime deployments. They are less about development practices and more about deployment automation and webserver configuration. Some tips might even make your application perform faster!

---

<a name="climbing-the-abstract-syntax-tree"></a>
## Climbing the Abstract Syntax Tree

By [James Titcumb](@baseUrl@/speakers.html#james-titcumb)

The new Abstract Syntax Tree (AST) in PHP 7 means the way our PHP code is being executed has changed. Understanding this new fundamental compilation step is key to understanding how our code is being run.

To demonstrate, James will show how a basic compiler works and how introducing an AST simplifies this process. We’ll look into how these magical time-warp techniques* can also be used in your code to introspect, analyse and modify code in a way that was never possible before.

After seeing this talk, you'll have a great insight as to the wonders of an AST, and how it can be applied to both compilers and userland code.

(*actual magic or time-warp not guaranteed)

---

<a name="large-scale-website-performance-optimisation-tricks"></a>
## Large-scale website performance optimisation tricks

By [Georgiana Gligor](@baseUrl@/speakers.html#georgiana-gligor)

Practical lessons learned while revamping a US airline website to resist huge Black Friday and Cyber Monday traffic values. Using HTTP status codes and PHP cleverly, we have made parallel requests possible, so that the user experience is greatly enhanced, and we pre-cache resource-consuming user searches. All on a solid PHP foundation.

---

<a name="application-metrics-with-prometheus"></a>
## Application metrics with prometheus

By [Rafael Dohms](@baseUrl@/speakers.html#rafael-dohms)

We all know not to poke at alien life forms in another planet, right? But what about metrics, do you know how to pick, measure and draw conclusions from them? In this talk we will cover Service Level Indicators (SLI), Objectives (SLO), and how to use Prometheus, an open-source system monitoring and alert platform, to measure and make sense of them. Let's get back to some real science.

---
