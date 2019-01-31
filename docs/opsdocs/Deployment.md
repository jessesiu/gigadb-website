# Deployment

## ops directory

The `ops` directory contains files relating to the deployment of GigaDB on 
servers. It currently contains 5 directories:
```
ops
├── configuration   # Contains config files and example environment variables
├── deployment      # Contains docker-compose files
├── infrastructure  # Contains Terraform and Ansible files
├── packaging       # Contains Dockerfiles (only for PHP containers at the mo)
└── scripts         # Contains executable configuration scripts
```

## GitLab CI/CD

GitLab provides a CI service used by GigaDB based on the 
[`.gitlab-ci.yml`](https://github.com/gigascience/gigadb-website/blob/develop/.gitlab-ci.yml)
file located at the root of the `gigadb-website` repository. A Runner in GitLab 
is configured to trigger the CI pipeline every time there is a code commit or 
push to the GitHub repository which is mirrored on GitLab. GitLab.com allows you 
to use Shared Runners provided by GitLab Inc which are virtual machines running 
on GitLab's infrastructure to build any project.

### Environments

#### AWS

The GitLab CI/CD pipeline has been tested to work with AWS so that GigaDB is 
deployable on an EC2 instance.

#### CNGB

TODO - CNGB has not been tested yet with the CI/CD pipeline.

### Variables

Variables are used to configure the CI/CD pipeline and the GigaDB application.
They can be defined as per-group or per-project variables which are not stored
in the code repository but are securely-passed to the GitLab Runner executing
jobs in the CI/CD pipeline. These variables might be security-sensitive 
passwords and credentials. 

#### Group variables

Group-level variables can be added by:

1. Navigating to your group’s Settings > CI/CD page.

2. Inputting variable keys and values in the Environment variables section. Any variables of subgroups will be inherited recursively.

#### Project variables

Project-level variables can be added by:

1. Navigating to your project’s Settings > CI/CD page.

2. Inputting variable keys and values in the Environment variables section.

### Pipeline

The CI/CD pipeline is described by the `.gitlab-ci.yml` file which defines a 
number of stages that can be performed in sequence by a GitLab Runner:

Stage | Job | When | Allow failure | Description
------|-----|------|---------------|------------
build | build_webapp | Always | No | Builds images from Dockerfiles and pushes them to the GitLab registry.
test  | run_all_tests | Always | No | Pulls built images from GitLab registry and runs all tests.
. | check_DAST | Manual | Yes | Performs Dynamic Application Security Testing (DAST).
security | check_SAST | Always | Yes | Performs Static Application Security Testing (SAST) to scan GigaDB dependencies for possible vulnerabilities.
conformance | check_coverage | Always| N/A |  Uses [Coveralls](https://coveralls.io) to detect which parts of gigadb-website code are not covered by its test suite.
. | check_PSR2 | Always | Yes | Check gigadb-website code adheres to PSR2 coding style guidelines using [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki)
. | check_PHPDoc | Always | Yes | Uses [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki) to detect violations of a defined coding standard.
staging | with_new_cert_deploy | Manual | No | This is run to generate GigaDB website’s HTTPS certificates the first time for a new environment.
. | deploy_app | Manual | No | Any subsequent deploy should use this job to deploy the GigaDB application and renew the HTTPS certificate at the same time.
live | | | | This stage needs to be developed in the .gitlab-ci.yml file.

The status of every pipeline is displayed in the Pipelines page.

### Deploying a build

Deployment of a build requires a server running a Docker daemon. Creation of a
server instance on AWS EC2 is done using [Terraform](https://www.terraform.io).
The provisioning of this EC2 server with a Docker daemon then performed using 
[Ansible](https://www.ansible.com). In addition, this Ansible stage posts the
following values to the variables below:

Variable | Description
---------|------------
staging_tlsauth_ca | Certificate authority for staging server
staging_tlsauth_cert | Public certificate for staging server
staging_tlsauth_key | Server key for the above certificate authority

#### AWS

TODO

#### CNGB

TODO

### Rollback a build

TODO

### Managing changes to the database

TODO