[codeigniter]: https://codeigniter.com/
[codeigniterrest]: https://github.com/chriskacerguis/codeigniter-restserver

# CIL_RS
**C**ell **I**mage **L**ibrary **R**EST **S**ervice

## Description
This project is funded by a grant from the National Institute of General Medical Sciences of the National Institutes of 
Health under award number 2R01GM082949. The goal of the Cell Image Library (CIL) is to create a valuable research tool 
to promote analysis and new discoveries. The CIL seeks images from all organisms, cell types, and processes, normal 
and pathological. Image quality should be as high as possible, within the limitations imposed by the then current state of readily available imaging technology 
and constraints imposed by the biological specimen.

Cell Image Library Rest Service (CIL_RS) implements the CIL internal REST API. This API allows the CIL website to 
retrieve and find documents from the Elasticsearch JSON search engine. CIL_RS uses [Basic HTTP Authentication](https://en.wikipedia.org/wiki/Basic_access_authentication) 
over [SSL](https://en.wikipedia.org/wiki/Transport_Layer_Security) for authentication.

## Requirements
* [CodeIgniter][codeigniter] (Built with 3.1.4)
* [CodeIgniter Rest Server][codeigniterrest] (Built with 3.0.0)

## Libraries
* PHP curl library
* PHP pgsql library

## Installation
See the [installation instructions](https://github.com/CRBS/CIL_RS/wiki/Installation)

## License
See [license.txt](https://github.com/CRBS/CIL_RS/blob/master/LICENSE.txt)

## Bugs
Please report them [here](https://github.com/CRBS/CIL_RS/issues)

## Contributing
If you would like to contribute to the CIL, please fork the repository and submit pull requests or contact us: wawong@ucsd.edu with the subject, CIL Contribution.








