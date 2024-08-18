# Reimagined-KMI: A Pure PHP Implementation of Command Line Pastebin

Reimagined-KMI is a project developed in pure PHP, aiming to replicate the functionality of a command line pastebin service.
It enables users to quickly share text snippets without the need for registration.

Visit the [live demo](http://kmi.example.com) to see Reimagined-KMI in action.

## Features

- **Anonymous Posting**: Share your snippets without needing to register.

## Limits

- **Storage Time**: Unlimited, but data may be pruned at any time.
- **Maximum Post Size**: Limited to 512KB.

## Getting Started

To use Reimagined-KMI, you'll need to configure the domain from which the service will be accessed. Follow these steps:

1. Clone the repository to your local machine.
2. Modify the `.env.example` file in the root directory of the project with your domain and rename it to `.env`.

## Usage Examples

#### Uploading a File

To upload a file named `hello-world.c`, execute the following command in your terminal:

```bash
cat hello-world.c | curl -F 'kmi=<-' https://yourdomain.com
```

After uploading, you'll receive a unique identifier (e.g., `IAmExample`). You can then access your snippet using this identifier.

#### Viewing Snippets

Retrieve the snippet content using curl with the following command:
```bash
curl https://yourdomain.com/IAmExample
```

## Running Locally

If you prefer to run the application locally instead of using Docker, ensure you have PHP version 7.4 or higher installed. Then, follow these steps:

1. Navigate to the root directory of the project in your terminal.
2. Execute the following command to start the application: ```sh ./start.sh```

The application should now be accessible at `http://0.0.0.0:8080`.

## Running with Docker

To deploy Reimagined-KMI using Docker, build the Docker image and run the container:
```bash
docker build -t reimagined-kmi-img .
docker run -d --name reimagined-kmi-cnt -p 8080:8080 reimagined-kmi-img:latest
```

The application should now be accessible at `http://0.0.0.0:8080`.

## License

This project is licensed under the MIT License. See the LICENSE file for details.
