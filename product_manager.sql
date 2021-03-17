-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 07/03/2021 às 06:17
-- Versão do servidor: 10.1.37-MariaDB
-- Versão do PHP: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `product_manager`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `data_system`
--

CREATE TABLE `data_system` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `keywords` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `date_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `token_access` varchar(255) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `data_system`
--

INSERT INTO `data_system` (`id`, `title`, `description`, `keywords`, `email`, `telefone`, `date_insert`, `date_update`, `facebook`, `instagram`, `twitter`, `token_access`, `whatsapp`) VALUES
(2, 'Forte Ferragens', 'Forte Ferragens', 'Forte Ferragens', 'contato@forteferragens.com.br', '(81) 99999-9999', '2019-11-26 16:39:06', '2021-03-07 04:53:30', '#', '#', '', 'Y7IcZrKN7DmTrHU36ZSsmVJd27NGPDHamrTjhRvjNMBL0J227Ipb9ayy14Q5hVSdQd7gXQbVlzlcDRX59WZx08jsp0qMQqiW95hZmtGUkhQELonetjcjSrItmAOOtiCffJquvakjtCoXgRrhUmNdqx', '(23) 1231-31231');

-- --------------------------------------------------------

--
-- Estrutura para tabela `log_system`
--

CREATE TABLE `log_system` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `description` text,
  `date_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` tinyint(1) DEFAULT NULL,
  `id_empresa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `root` int(11) DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `order_by` int(5) DEFAULT '0',
  `controller` varchar(200) DEFAULT NULL,
  `icon` varchar(200) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `variations` varchar(255) DEFAULT NULL,
  `permissions` varchar(255) DEFAULT NULL,
  `tipos_empresa` varchar(255) DEFAULT NULL,
  `status_active` int(1) NOT NULL DEFAULT '1',
  `date_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `menu`
--

INSERT INTO `menu` (`id`, `root`, `title`, `order_by`, `controller`, `icon`, `description`, `keywords`, `variations`, `permissions`, `tipos_empresa`, `status_active`, `date_insert`, `date_update`) VALUES
(1, 0, 'Dashboard', 1, 'dashboard', 'ti-home', '', '', '', '1,4,5,6,7', NULL, 1, '2019-04-08 11:44:46', '2020-10-06 01:45:21'),
(2, 0, 'Configurações', 960, '#', 'ti-settings', '', '', '', '1,4,6', '1', 1, '2019-04-08 15:14:18', '2020-03-09 19:34:12'),
(3, 2, 'SMTP', 501, 'smtp', 'ti-settings', '', '', '', '1,4', '1', 1, '2019-04-08 12:14:46', '2019-04-08 12:14:46'),
(4, 2, 'Menu', 502, 'menu', 'ti-settings', '', '', '', '1,4', '1', 1, '2019-04-08 12:15:14', '2019-04-08 12:15:14'),
(5, 2, 'Dados', 503, 'dados', 'ti-settings', '', '', '', '1,4', NULL, 1, '2019-04-08 12:31:02', '2021-03-07 04:46:26'),
(6, 0, 'Gestor de Usuários', 10, '#', 'ti-user', '', '', '', '1,4', NULL, 1, '2019-04-08 13:14:18', '2020-05-28 00:59:17'),
(7, 6, 'Master', 11, 'usuarios/master', 'ti-user', '', '', '', '1,4', '1', 1, '2019-04-08 13:14:49', '2019-11-26 14:45:09'),
(8, 6, 'Desenvolvedores', 12, 'usuarios/desenvolvedor', 'ti-user', '', '', '', '1,4', '1,2', 1, '2019-04-08 13:15:16', '2019-11-26 14:45:24'),
(13, 12, 'Agências', 21, 'empresas/agencia', 'ti-briefcase', '', '', '', '1,4', '1,2', 1, '2019-04-08 13:44:33', '2019-11-26 15:01:20'),
(101, 2, 'Tipos de Usuário', 520, 'tipos/user', 'ti-settings', '', '', '', '1,4', '1', 1, '2019-11-26 14:14:37', '2019-11-26 14:14:37'),
(108, 107, 'Configurações', 1, 'empresas/configuracoes/{token_company}', 'ti-briefcase', '', '', '', '1,4,6,7', '1,2', 1, '2019-11-28 14:12:14', '2020-01-31 20:19:15'),
(109, 107, 'Endereços', 2, 'empresa/enderecos/{token_company}/list', 'ti-briefcase', '', '', '', '1,4,6,7', '1,2', 1, '2019-11-28 14:14:18', '2020-01-31 20:19:21'),
(110, 107, 'Funcionários', 3, 'empresas/usuarios/{token_company}', 'ti-briefcase', '', '', '', '1,4,6,7', '1,2', 1, '2019-11-28 14:15:38', '2020-01-31 20:19:26'),
(123, 122, 'Pedidos', 1, 'avaliacoes/pedido', 'ti-star', '', '', '', '1,4,6,7', '1,2', 1, '2020-02-21 17:12:56', '2020-02-21 17:12:56'),
(124, 122, 'Vouchers', 2, 'avaliacoes/voucher', 'ti-star', '', '', '', '1,4,6,7', '1,2', 1, '2020-02-21 17:13:22', '2020-02-21 17:13:22'),
(127, 12, 'Lojas', 22, 'empresas/loja', 'ti-briefcase', '', '', '', '1,4', '1', 1, '2020-04-22 16:12:51', '2020-04-27 12:03:53'),
(140, 138, 'Dados', 1, 'configuracoes/site/data', 'ti-server', '', '', '', '1,4', '1', 1, '2020-04-27 12:13:26', '2020-04-27 12:13:26'),
(141, 138, 'Textos', 2, 'configuracoes/site/textos', 'ti-server', '', '', '', '1,4', '1', 1, '2020-04-27 12:41:15', '2020-04-27 12:41:15'),
(144, 143, 'Categorias', 1, 'produtos/categorias', 'ti-package', '', '', '', '1,4', '1', 1, '2020-04-28 18:24:57', '2020-04-28 18:24:57'),
(146, 143, 'Criar/Editar', 2, 'produtos', 'ti-package', '', '', '', '1,4,13', '1,3', 1, '2020-05-06 18:51:40', '2020-05-06 18:51:40'),
(148, 147, 'Letra', 1, 'tamanhos/0', 'ti-ruler-alt-2', '', '', '', '1,4', '1', 1, '2020-05-07 13:17:34', '2020-05-07 13:17:34'),
(149, 147, 'Número', 2, 'tamanhos/1', 'ti-ruler-alt-2', '', '', '', '1,4', '1', 1, '2020-05-07 13:18:51', '2020-05-07 13:18:51'),
(153, 12, 'Anunciantes', 23, 'empresas/anunciante', 'ti-briefcase', '', '', '', '1,4', '1', 1, '2020-05-11 14:54:33', '2020-05-11 14:54:33'),
(154, 12, 'Padrão', 30, 'empresas/padrao', 'ti-briefcase', '', '', '', '1,4', '1', 1, '2020-05-11 14:55:19', '2020-05-11 14:55:19'),
(156, 155, 'Anunciantes', 1, 'comercial/anunciantes', 'ti-shopping-cart-full', '', '', '', '1,4', '1', 1, '2020-05-11 17:31:49', '2020-05-12 11:49:04'),
(157, 155, 'Categorias', 2, 'comercial/categorias', 'ti-shopping-cart-full', '', '', '', '1,4', '1', 1, '2020-05-11 17:32:32', '2020-05-11 17:32:32'),
(168, 167, 'Letra', 1, 'tamanhos/0', 'ti-ruler-alt-2', '', '', '', '1,4', NULL, 1, '2020-06-07 04:05:41', '2020-06-07 04:05:41'),
(169, 167, 'Número', 2, 'tamanhos/1', 'ti-ruler-alt-2', '', '', '', '1,4', NULL, 1, '2020-06-07 04:06:12', '2020-06-07 04:06:12'),
(172, 171, 'Relatórios de Vendas', 1, 'relatorios/vendas', 'ti-bar-chart', '', '', '', '1,4,5', NULL, 1, '2020-06-28 04:49:27', '2020-06-28 04:49:27'),
(173, 6, 'Vendedores', 14, 'usuarios/vendedor', 'ti-user', '', '', '', '1,4,5', NULL, 1, '2020-07-28 01:06:39', '2020-07-28 01:06:39'),
(176, 171, 'Relatórios Produtos', 2, 'relatorios/produtos', 'ti-bar-chat', '', '', '', '1,4,5', NULL, 1, '2020-08-01 03:39:44', '2020-08-01 03:40:17'),
(179, 171, 'Maiores Compradores', 3, 'relatorios/clientes', 'ti-bar-chat', '', '', '', '1,4,5', NULL, 1, '2020-08-21 03:12:09', '2020-08-21 03:12:09'),
(180, 171, 'Estoque Atual', 4, 'relatorios/estoque', '', '', '', '', '1,4,5', NULL, 1, '2020-08-23 03:26:48', '2020-08-23 03:26:48'),
(183, 181, 'Criar/Visualizar', 1, 'prospeccao-vendas', 'ti-filter', '', '', '', '1,4,5,6,7', NULL, 1, '2020-08-26 02:30:03', '2020-08-26 02:30:03'),
(184, 181, 'Permissões', 2, 'prospeccao-vendas/usuarios', 'ti-filter', '', '', '', '1,4,5,6,7', NULL, 1, '2020-08-26 02:30:43', '2020-08-26 02:30:43'),
(185, 181, 'Pedidos', 3, 'prospeccao-vendas/pedidos', 'ti-filter', '', '', '', '1,4', NULL, 1, '2020-09-12 18:17:31', '2020-09-12 18:17:31'),
(190, 200, 'Gestor de Site', 900, '#', 'ti-world', '', '', '', '1,4,8', NULL, 1, '2020-11-04 03:39:57', '2020-12-16 04:13:59'),
(191, 190, 'Textos', 1, 'site/itens/textos', 'ti-world', '', '', '', '1,4,8', NULL, 1, '2020-11-04 03:40:37', '2020-11-04 03:41:23'),
(192, 190, 'Clientes', 3, 'site/itens/clientes', 'ti-world', '', '', '', '1,4,8', NULL, 1, '2020-11-04 03:41:14', '2020-11-04 03:41:14'),
(193, 190, 'Serviços', 2, 'site/itens/servicos', 'ti-world', '', '', '', '1,4,8', NULL, 1, '2020-11-04 03:41:53', '2020-11-04 03:41:53'),
(194, 190, 'Dúvidas', 4, 'site/itens/faqs', '', '', '', '', '1,4,8', NULL, 1, '2020-11-04 03:44:34', '2020-11-04 03:44:34'),
(195, 190, 'Slides', 5, 'site/itens/slides', 'ti-world', '', '', '', '1,4,8', NULL, 1, '2020-11-04 03:45:07', '2020-11-04 03:45:07'),
(196, 200, 'Email Marketing', 2, 'email-marketing', 'ti-email', '', '', '', '1,4,8', NULL, 1, '2020-11-13 05:01:47', '2020-12-16 04:13:42'),
(198, 197, 'Fornecedores', 6, 'financeiro/fornecedor', 'ti-money', '', '', '', '1,4,8', NULL, 1, '2020-12-15 02:34:19', '2020-12-15 02:34:19'),
(199, 197, 'Tipos de Conta', 8, 'financeiro/tipos-conta', 'ti-money', '', '', '', '1,4,8', NULL, 1, '2020-12-15 02:46:22', '2020-12-15 02:46:22'),
(201, 197, 'Contas', 1, '#', 'ti-money', '', '', '', '1,4,8', NULL, 1, '2020-12-17 04:17:16', '2020-12-17 04:19:49'),
(202, 201, 'Pagar', 1, 'financeiro/contas/pagar', 'ti-money', '', '', '', '1,4,8', NULL, 1, '2020-12-17 04:18:10', '2020-12-17 04:19:33'),
(203, 201, 'Receber', 2, 'financeiro/contas/receber', 'ti-money', '', '', '', '1,4,8', NULL, 1, '2020-12-17 04:18:40', '2020-12-17 04:19:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `smtp`
--

CREATE TABLE `smtp` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `autenticar` int(1) DEFAULT '0',
  `protocol` varchar(5) DEFAULT NULL,
  `host` varchar(255) NOT NULL,
  `date_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `port` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `smtp`
--

INSERT INTO `smtp` (`id`, `name`, `email`, `username`, `pass`, `autenticar`, `protocol`, `host`, `date_insert`, `port`) VALUES
(1, 'Suporte | Papoodin', 'envios@papoodin.com.br', 'envios@papoodin.com.br', 'b%0amW}~([hY', 1, 'tls', 'mail.papoodin.com.br', '2019-11-27 20:04:32', '587'),
(2, 'Suporte | RB Cargas', 'envios@rbcargas.com', 'AKIAUAWCF5UKY4F3S3KL', 'BGwHSGI6ID9IU9QEGtFxGMCHFfjJnVZRoMl5VvSMuy8D', 1, 'tls', 'email-smtp.us-east-1.amazonaws.com', '2020-04-23 15:03:26', '587'),
(3, 'Feiras da Sulanca Marketplace', 'envios@feirasdasulanca.com.br', 'AKIAUAWCF5UKY4F3S3KL', 'BGwHSGI6ID9IU9QEGtFxGMCHFfjJnVZRoMl5VvSMuy8D', 1, 'tls', 'email-smtp.us-east-1.amazonaws.com', '2020-04-27 13:52:41', '587'),
(4, 'Feiras da Sulanca Marketplace', 'envios@feirasdasulanca.com.br', 'AKIAUAWCF5UKY4F3S3KL', 'BGwHSGI6ID9IU9QEGtFxGMCHFfjJnVZRoMl5VvSMuy8D', 1, 'tls', 'email-smtp.us-east-1.amazonaws.com', '2020-04-27 13:53:03', '587'),
(5, 'Feiras da Sulanca Marketplace', 'envios@feirasdasulanca.com.br', 'AKIAUAWCF5UKY4F3S3KL', 'BGwHSGI6ID9IU9QEGtFxGMCHFfjJnVZRoMl5VvSMuy8D', 1, 'tls', 'email-smtp.us-east-1.amazonaws.com', '2020-04-27 13:54:22', '587'),
(6, 'teste', 'teste@teste.com', 'teste', '],(ah$c-n[PE', 1, 'tls', 'teste', '2020-05-24 03:15:08', '587'),
(7, 'Faz Agilizar', 'envios@fazagilizar.com.br', 'envios@fazagilizar.com.br', '],(ah$c-n[PE', 1, 'tls', 'mail.fazagilizar.com.br', '2020-10-13 02:16:07', '465'),
(8, 'Faz Agilizar', 'envios@fazagilizar.com.br', 'envios@fazagilizar.com.br', '],(ah$c-n[PE', 1, 'tls', 'mail.fazagilizar.com.br', '2020-10-13 02:45:55', '587'),
(9, 'Faz Agilizar', 'envios@fazagilizar.com.br', 'AKIAYNMZL2RWEHJLX7MI', 'BNM7nSBW+eP8Ab0pINki70KulcBmUsvTh5BuLmNtg00f', 1, 'tls', 'email-smtp.us-east-2.amazonaws.com', '2020-11-25 02:48:20', '587'),
(10, 'Faz Agilizar', 'envios@fazagilizar.com.br', 'AKIAYNMZL2RWEHJLX7MI', 'BNM7nSBW+eP8Ab0pINki70KulcBmUsvTh5BuLmNtg00f', 1, 'tls', 'email-smtp.us-east-2.amazonaws.com', '2020-11-25 02:49:47', '587');

-- --------------------------------------------------------

--
-- Estrutura para tabela `type_user`
--

CREATE TABLE `type_user` (
  `id` int(11) NOT NULL,
  `ref` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_by` int(5) NOT NULL DEFAULT '0',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `type_user`
--

INSERT INTO `type_user` (`id`, `ref`, `order_by`, `title`, `date_insert`) VALUES
(1, 'master', 1, 'Master', '2019-11-26 13:50:45'),
(4, 'desenvolvedor', 2, 'Desenvolvedor', '2019-11-26 13:59:07'),
(5, 'cliente', 3, 'Cliente', '2020-05-24 03:15:21'),
(6, 'vendedor', 4, 'Vendedor', '2020-07-28 01:06:14'),
(7, 'parceiro', 5, 'Parceiro', '2020-08-25 02:52:27'),
(8, 'suporte', 6, 'Suporte', '2020-09-12 04:42:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `id_type` int(11) DEFAULT NULL,
  `token` varchar(200) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `login` varchar(25) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `date_insert` timestamp NULL DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `id_type`, `token`, `nome`, `email`, `login`, `telefone`, `senha`, `date_insert`, `status`) VALUES
(1, 4, '5ubBffMlxkM1GLcZD9cI', 'Fábio Henrique DA sILVA SIQUEIRA', 'fabio.siqueira1222@gmail.com', 'fabiosiqueira12', '(87) 99811-1645', 'a03c32ff8a4da40b9684ec0df2442a40', '2020-05-21 03:00:00', 1),
(2, 1, '1EtPYWYmUzb4sKbVMZl11MzND4yKD64wVoEtklRUFmo4phO82sX9vx9Yx1xJ0gC4Uvy2K1vPpAniRQseHThAzvbGDh9dof1yjnTyJCR8nKW1ugfw1ZMWTD715ecvMHTBVJLZmUhCZeJuiBFX3WOASy', 'Welliton Alves', 'wellitonalves@gmail.com', 'welliton', '(81) 99999-9999', '4badaee57fed5610012a296273158f5f', '2020-05-24 03:06:40', 1),
(3, 1, 'jjSaA5XLcp7583gY3uKcfVkHKwJmGqxq2jp1HUmQmLeGGZWpcpsFpeotdWoAxN2ZUHr6rAwxp7QVCgTMVb8TJZsY7ZpR6rvCDTW6VZ65Pi4b6TLhxZPTOzjlEDxAgsD3hqn3iZ6yVC1rtcWotaS3wI', 'Cliente 01', 'Cliente01@teste.com', 'cliente01', '(12) 31312-3123', '4badaee57fed5610012a296273158f5f', '2020-05-26 03:40:07', 1),
(6, 6, 'qp9fPy2PyEvIixZkqkNo3Rp0hxHdTQXD6KxXV48pfMY5ArkMXK8LnmF4cpLfFD0rM5p4CsZ58tjHufDSDDvxHU775J9oIsB8y5lBDIwZ87BTThpUYIWO1ne2kvp2jHsiXsXv966C3Vea77vaEfCbZT', 'Vendedor 01', '5f3c7d1f05786@fazagilizar.com.br', 'vendedor-01', '(81) 99999-9999', '4badaee57fed5610012a296273158f5f', '2020-08-19 06:15:11', 0),
(9, 6, 'MG1eQwcrza4VXR8OLZlMvP3BeCxrkxB4nvE3u33MkkrvDMg1ZzmxKDZGJV4qL6A3SnlivrPNTMb8zQwWY4ylxispnefQNv54SWROPlYc4Jvxw2Dp8x6kL3aFVl1zbCbFDAVL7Mj994gIW2IVe0CHFG', 'Vendedor Usuário 02', 'vendedor02@teste.com', 'vendedor-02', '(81) 99999-9999', '4badaee57fed5610012a296273158f5f', '2020-08-19 03:48:27', 1),
(10, 7, 'CRKQkgaicDlXcOfQw906goXgcfblWa333PUSPg1kb4ai05b1IbLm926Nrrpown49V49ybUKHsq3z31L1zTSoVJVWf2XLhMZ7WVVi4BmNDzgvXn2VvHE0zjdPtJarlqJm7yiLDKV2cOpGEUi6Bg8QGK', 'Parceiro 01', 'parceiro@fazagilizar.com.br', 'parceiro-01', '(81) 99999-9999', '4badaee57fed5610012a296273158f5f', '2020-08-25 03:18:10', 1),
(11, 5, '9tjliXrac9GHLnmj9jSoYuBiVmct8eBiKBkUgXhJtn6M5x4xpJXAXRaHgela4wkOGrEVTZWgFSOJ4R3cpN0iQMkfXMOgXY8bDGbzbaBamQ9FomNaDNv5WF52sljtFG3hQyEtYhlJ5vfrF0QrKjA6Do', 'Cliente teste', 'cliente@teste.com', 'teste', '(81) 99999-9999', '4badaee57fed5610012a296273158f5f', '2020-09-01 02:37:05', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_recover_password`
--

CREATE TABLE `usuario_recover_password` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `date_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_expire` timestamp NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `usuario_recover_password`
--

INSERT INTO `usuario_recover_password` (`id`, `token`, `id_user`, `email`, `date_insert`, `date_expire`) VALUES
(2, '1e8a4c4af41b1110b0c6ba1abdb518a75f079a50ff1d480c5f7e7583bc2f89adae7e0542198873598c0e0b7c4bf37e203761', 1, 'fabio.siqueira1222@gmail.com', '2020-10-13 02:51:07', '2020-10-13 03:51:07'),
(3, '9acd4cdb9c83a1b6b083fb3fb554b278117279b8c3534f371c01ef0ef2668dcf267f372fcd827dfae564be269cb4ea0d5f32', 1, 'fabio.siqueira1222@gmail.com', '2020-11-25 02:48:58', '2020-11-25 03:48:58'),
(4, '0c10f1aa10f87e85678b3fbc6deb623bfd286bcb8c996a0f67e326246365e99a5b9a91c5d699f71ae49533279b8c2031cfbc', 1, 'fabio.siqueira1222@gmail.com', '2020-11-27 01:02:00', '2020-11-27 02:02:00');

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `data_system`
--
ALTER TABLE `data_system`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `log_system`
--
ALTER TABLE `log_system`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `smtp`
--
ALTER TABLE `smtp`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `type_user`
--
ALTER TABLE `type_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ref` (`ref`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `id_type` (`id_type`);

--
-- Índices de tabela `usuario_recover_password`
--
ALTER TABLE `usuario_recover_password`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `id_user` (`id_user`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `data_system`
--
ALTER TABLE `data_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `log_system`
--
ALTER TABLE `log_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT de tabela `smtp`
--
ALTER TABLE `smtp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `type_user`
--
ALTER TABLE `type_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `usuario_recover_password`
--
ALTER TABLE `usuario_recover_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
