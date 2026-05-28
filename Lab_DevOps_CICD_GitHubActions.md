UCR

IF7100 - Ingeniería del Software I
Sede de Guanacaste, Recinto de Liberia | I Ciclo 2026

Lab DevOps

1. Introducción y Contexto

En este laboratorio aplicará los conceptos de DevOps y automatización de pipelines CI/CD (Integración
Continua  y  Entrega  Continua)  utilizando  GitHub  Actions.  El  contexto  de  trabajo  será  el  proyecto
BovWeight  CR, la aplicación móvil para estimación de peso bovino que ha servido de hilo conductor
durante todo el curso.

flujos  de  automatización  que  validan

Configurara
la  calidad  del  código,  ejecutan  pruebas
automatizadas y publican artefactos de despliegue, cubriendo los tres componentes del sistema: el API
REST en Laravel, el microservicio de ML en Python/Flask, y la aplicación móvil en Ionic/Vue 3.

1.2 Prerrequisitos Técnicos

Antes de iniciar el laboratorio, verifique que cuenta con:

•  Cuenta activa en GitHub (github.com) con acceso a GitHub Actions.

•  PHP 8.2+ con Composer, Python 3.11+ con pip, y Node.js 20+.

•  Repositorios de BovWeight CR creados (bovweight-api, bovweight-ml, bovweight-app).

i

Los repositorios de trabajo pueden ser repositorios propios o forks del repositorio de la
clase.

2. Marco Teórico Resumido

2.1 GitHub Actions

GitHub  Actions  es  la  plataforma  CI/CD  integrada  en  GitHub. Los pipelines se definen como archivos
YAML dentro de la carpeta .github/workflows/ del repositorio. Los conceptos clave son:

Concepto

Workflow

Event/Trigger

Job

Step

Action

Runner

Descripción

Proceso automatizado completo, definido en un archivo YAML.

Evento que dispara el workflow (push, pull_request, schedule,
workflow_dispatch).

Unidad de ejecución dentro del workflow. Los jobs corren en paralelo
por defecto.

Tarea individual dentro de un job. Se ejecutan en secuencia.

Componente reutilizable que encapsula lógica (ej:
actions/checkout@v4).

Servidor donde se ejecuta el job (ubuntu-latest, windows-latest,
macos-latest).

IF7100 | Lab: DevOps y CI/CD con GitHub Actions

Página 1 de 3

UCR

IF7100 - Ingeniería del Software I
Sede de Guanacaste, Recinto de Liberia | I Ciclo 2026

Lab DevOps

Concepto

Secret

Descripción

Variable cifrada para guardar credenciales (API keys, tokens,
passwords).

3. Ejercicio 1 - Pipeline de CI para el API Laravel

3.1 Descripción

Configura el primer pipeline de CI para el repositorio bovweight-api. El pipeline deberá ejecutarse cada
vez  que  se  realice  un  push  a  las  ramas  main  y  develop,  y  en  cada  pull_request  hacia  main.
Automáticamente levantará una base de datos MySQL de prueba, ejecutará las migraciones y correrá
la suite de pruebas PHPUnit.

3.2 Tarea

Dentro  del  repositorio  bovweight-api,  cree  la  carpeta  .github/workflows/  y  dentro  de  ella el archivo
ci.yml con el siguiente contenido:

3.3 Actividad de Análisis

Una vez que el pipeline haya corrido al menos una vez (exitoso o fallido), responda en el informe:

1.  ¿Cuánto tiempo tardó el pipeline en completarse? Identifique el step más lento.

2.  Que ocurre con el pull_request si alguna prueba falla? Adjunte captura de pantalla.

3.  ¿Por qué se usa un servicio MySQL en lugar de SQLite para las pruebas? Argumente con base

en los requisitos no funcionales de BovWeight CR.

4.  Que ventaja tiene usar actions/checkout@v4 frente a clonar manualmente el repo?

4. Ejercicio 2 - Estrategia de Ramas y Branch Protection

6.1 Descripción

En  equipo  (2-3  personas),  diseñarán  e  implementarán  una  estrategia  de  ramas  para  BovWeight CR
que integre el pipeline de CI como puerta de calidad obligatoria antes de cualquier merge a main.

6.2 Estrategia de Ramas Propuesta

Rama

Proposito

Reglas de proteccion

IF7100 | Lab: DevOps y CI/CD con GitHub Actions

Página 2 de 3

UCR

IF7100 - Ingeniería del Software I
Sede de Guanacaste, Recinto de Liberia | I Ciclo 2026

Lab DevOps

main

Código en producción

PR obligatorio + CI verde + 1 revisor +
no force push

develop

Integración continua

CI obligatorio, merge squash, 0
revisores

feature/*

Nuevas funcionalidades

CI en cada push, merge a develop

hotfix/*

Correcciones urgentes

Merge directo a main y develop

6.3 Tarea

Configure las Branch Protection Rules en GitHub: navegue a Settings > Branches > Add Rule para la
rama main y habilite:

•     Require a pull request before merging

•     Require status checks to pass before merging (seleccione los jobs del CI)

•     Require branches to be up to date before merging

•     Do not allow bypassing the above settings

Luego,  cree  una  rama  feature/test-protection,  haga  un cambio que rompa una prueba, y verifique
que GitHub bloquea el PR. Incluya la captura en el informe.

IF7100 | Lab: DevOps y CI/CD con GitHub Actions

Página 3 de 3

